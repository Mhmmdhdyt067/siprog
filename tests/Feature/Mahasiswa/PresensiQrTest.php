<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PresensiQrTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: PracticumSession, 1: User, 2: string} */
    private function context(): array
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $classroom = Classroom::create([
            'code' => 'ALPRO-A',
            'name' => 'ALPRO-A',
            'course_name' => 'Algoritma Pemrograman',
            'schedule_day' => 'Senin',
            'schedule_time' => '08:00 - 10:30',
            'room' => 'Lab 1',
        ]);

        $student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $classroom->id,
        ]);

        $token = Str::random(40);

        $session = PracticumSession::create([
            'classroom_id' => $classroom->id,
            'created_by' => $admin->id,
            'meeting_number' => 1,
            'title' => 'Pengenalan',
            'session_date' => now()->toDateString(),
            'qr_token' => $token,
            'qr_expires_at' => now()->addHours(3),
            'status' => 'active',
        ]);

        return [$session, $student, $token];
    }

    public function test_old_qr_token_is_rejected_after_rotation(): void
    {
        [$session, $student, $oldToken] = $this->context();
        $admin = $session->creator;

        $this->travel(30)->seconds();

        $this->actingAs($admin)
            ->postJson(route('sessions.qr.refresh', $session))
            ->assertOk()
            ->assertJsonPath('rotated', true);

        $newToken = $session->fresh()->qr_token;

        $this->assertNotSame($oldToken, $newToken);
        $this->assertDatabaseMissing('qr_tokens', ['token' => $oldToken]);
        $this->assertDatabaseHas('qr_tokens', ['token' => $newToken]);
        $this->assertSame(1, $session->qrTokens()->count());

        $this->actingAs($student)
            ->postJson(route('mahasiswa.presensi.scan'), ['qr_data' => $oldToken])
            ->assertNotFound()
            ->assertJsonPath('success', false);

        $this->assertDatabaseMissing('attendances', [
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
        ]);

        $this->actingAs($student)
            ->postJson(route('mahasiswa.presensi.scan'), ['qr_data' => $newToken])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('attendances', [
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'status' => 'hadir',
        ]);
    }

    public function test_two_tabs_do_not_cancel_each_other_token(): void
    {
        [$session] = $this->context();
        $admin = $session->creator;

        $this->travel(30)->seconds();

        $this->actingAs($admin)
            ->postJson(route('sessions.qr.refresh', $session))
            ->assertOk()
            ->assertJsonPath('rotated', true);

        $token = $session->fresh()->qr_token;

        // Tab kedua polling di saat yang hampir sama: tidak boleh merotasi ulang.
        $this->actingAs($admin)
            ->postJson(route('sessions.qr.refresh', $session))
            ->assertOk()
            ->assertJsonPath('rotated', false);

        $this->assertSame($token, $session->fresh()->qr_token);

        // Tombol manual tetap boleh memaksa token baru.
        $this->actingAs($admin)
            ->postJson(route('sessions.qr.refresh', $session).'?force=1')
            ->assertOk()
            ->assertJsonPath('rotated', true);

        $this->assertNotSame($token, $session->fresh()->qr_token);
    }

    public function test_scanning_twice_keeps_a_single_attendance_row(): void
    {
        [$session, $student, $token] = $this->context();

        foreach ([1, 2] as $attempt) {
            $this->actingAs($student)
                ->postJson(route('mahasiswa.presensi.scan'), ['qr_data' => $token])
                ->assertOk()
                ->assertJsonPath('success', true);
        }

        $this->assertSame(1, Attendance::where('practicum_session_id', $session->id)
            ->where('user_id', $student->id)
            ->count());
    }

    public function test_scanning_marks_hadir_when_attendance_already_exists_as_other_status(): void
    {
        [$session, $student, $token] = $this->context();

        Attendance::create([
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'status' => 'alpha',
            'scanned_at' => now()->subDay(),
        ]);

        $this->actingAs($student)
            ->postJson(route('mahasiswa.presensi.scan'), ['qr_data' => $token])
            ->assertOk()
            ->assertJsonPath('success', true);

        $attendance = Attendance::where('practicum_session_id', $session->id)
            ->where('user_id', $student->id)
            ->sole();

        $this->assertSame('hadir', $attendance->status);
        $this->assertTrue($attendance->scanned_at->greaterThan(now()->subMinute()));
    }
}
