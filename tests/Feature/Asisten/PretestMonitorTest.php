<?php

namespace Tests\Feature\Asisten;

use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PretestMonitorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assistant;
    private Classroom $own;
    private Classroom $other;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->assistant = User::factory()->create(['role' => UserRole::ASISTEN]);
        $this->own = $this->classroom('ALPRO-A');
        $this->other = $this->classroom('ALPRO-B');
        $this->assistant->assistedClassrooms()->attach($this->own->id);
    }

    public function test_monitor_lists_only_attempts_from_assisted_classrooms(): void
    {
        [$ownStudent] = $this->attempt($this->own, 'Praktikan Sendiri');
        [$otherStudent] = $this->attempt($this->other, 'Praktikan Kelas Lain');

        $this->actingAs($this->assistant)
            ->get(route('admin.pretest.monitor'))
            ->assertOk()
            ->assertSee($ownStudent->name)
            ->assertDontSee($otherStudent->name);
    }

    public function test_monitor_data_returns_rendered_partials(): void
    {
        $this->attempt($this->own, 'Praktikan Sendiri');

        $this->actingAs($this->assistant)
            ->getJson(route('admin.pretest.monitor.data'))
            ->assertOk()
            ->assertJsonStructure(['message', 'html' => ['#monitor-summary', '#monitor-list']]);
    }

    public function test_monitor_hides_finished_attempts(): void
    {
        [$student, $attendance] = $this->attempt($this->own, 'Sudah Selesai');
        $attendance->update([
            'pretest_finished_at' => now(),
            'pretest_deadline' => now()->subMinute(),
        ]);

        $this->actingAs($this->assistant)
            ->get(route('admin.pretest.monitor'))
            ->assertOk()
            ->assertDontSee($student->name);
    }

    public function test_monitor_shows_an_empty_state(): void
    {
        $this->actingAs($this->assistant)
            ->get(route('admin.pretest.monitor'))
            ->assertOk()
            ->assertSee('Belum ada praktikan yang sedang mengerjakan pre-test.');
    }

    public function test_student_cannot_open_the_monitor(): void
    {
        $student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $this->own->id,
        ]);

        $this->actingAs($student)
            ->get(route('admin.pretest.monitor'))
            ->assertForbidden();
    }

    public function test_assistant_can_warn_a_student(): void
    {
        [$student] = $this->attempt($this->own, 'Praktikan Sendiri');
        $attendance = Attendance::where('user_id', $student->id)->firstOrFail();

        $this->actingAs($this->assistant)
            ->postJson(route('admin.pretest.monitor.warn', $attendance), [
                'message' => 'Fokus ke layar Anda.',
            ])
            ->assertOk()
            ->assertJsonStructure(['message', 'html']);

        $this->assertSame('Fokus ke layar Anda.', $attendance->fresh()->pretest_warning);

        $this->actingAs($student)
            ->getJson(route('mahasiswa.pretest.state', $attendance->session))
            ->assertOk()
            ->assertJsonPath('warning.message', 'Fokus ke layar Anda.');
    }

    public function test_assistant_can_unlock_a_locked_attempt(): void
    {
        [$student] = $this->attempt($this->own, 'Praktikan Sendiri');
        $attendance = Attendance::where('user_id', $student->id)->firstOrFail();
        $attendance->update([
            'pretest_violations' => 2,
            'pretest_last_violation_at' => now(),
            'pretest_locked_at' => now(),
        ]);

        $this->actingAs($this->assistant)
            ->postJson(route('admin.pretest.monitor.unlock', $attendance))
            ->assertOk();

        $attendance->refresh();
        $this->assertNull($attendance->pretest_locked_at);
        $this->assertSame(0, $attendance->pretest_violations);
        $this->assertNull($attendance->pretest_last_violation_at);

        $this->actingAs($student)
            ->get(route('mahasiswa.pretest.show', $attendance->session))
            ->assertOk();
    }

    public function test_unlock_does_not_revive_an_expired_pretest(): void
    {
        [$student] = $this->attempt($this->own, 'Praktikan Sendiri');
        $attendance = Attendance::where('user_id', $student->id)->firstOrFail();
        $attendance->update([
            'pretest_started_at' => now()->subMinutes(20),
            'pretest_deadline' => now()->subMinutes(5),
            'pretest_locked_at' => now(),
        ]);

        $this->actingAs($this->assistant)
            ->postJson(route('admin.pretest.monitor.unlock', $attendance))
            ->assertOk();

        $this->actingAs($student)
            ->get(route('mahasiswa.pretest.show', $attendance->session))
            ->assertViewIs('mahasiswa.pretest-closed');
    }

    public function test_unlock_rejects_a_finished_attempt(): void
    {
        [$student] = $this->attempt($this->own, 'Praktikan Sendiri');
        $attendance = Attendance::where('user_id', $student->id)->firstOrFail();
        $attendance->update(['pretest_finished_at' => now()]);

        $this->actingAs($this->assistant)
            ->postJson(route('admin.pretest.monitor.unlock', $attendance))
            ->assertStatus(422);
    }

    public function test_assistant_cannot_warn_or_unlock_another_classroom(): void
    {
        [$student] = $this->attempt($this->other, 'Praktikan Kelas Lain');
        $attendance = Attendance::where('user_id', $student->id)->firstOrFail();

        $this->actingAs($this->assistant)
            ->postJson(route('admin.pretest.monitor.warn', $attendance), ['message' => 'Halo'])
            ->assertForbidden();

        $this->actingAs($this->assistant)
            ->postJson(route('admin.pretest.monitor.unlock', $attendance))
            ->assertForbidden();
    }

    private function classroom(string $code): Classroom
    {
        return Classroom::create([
            'code' => $code,
            'name' => $code,
            'course_name' => 'Algoritma Pemrograman',
            'schedule_day' => 'Senin',
            'schedule_time' => '08:00 - 10:30',
            'room' => 'Lab 1',
        ]);
    }

    private function attempt(Classroom $classroom, string $name): array
    {
        $student = User::factory()->create([
            'name' => $name,
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $classroom->id,
        ]);

        $session = PracticumSession::create([
            'classroom_id' => $classroom->id,
            'created_by' => $this->admin->id,
            'meeting_number' => 1,
            'title' => 'Pertemuan ' . $classroom->code,
            'session_date' => now()->toDateString(),
            'qr_token' => Str::random(40),
            'status' => 'active',
        ]);

        $attendance = Attendance::create([
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'status' => 'hadir',
            'scanned_at' => now(),
            'pretest_started_at' => now(),
            'pretest_deadline' => now()->addMinutes(10),
        ]);

        return [$student, $attendance];
    }
}
