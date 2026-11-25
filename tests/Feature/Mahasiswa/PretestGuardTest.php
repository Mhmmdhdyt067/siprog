<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\PretestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PretestGuardTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private PracticumSession $session;
    private Attendance $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = Classroom::create([
            'code' => 'ALPRO-A',
            'name' => 'ALPRO A',
            'course_name' => 'Algoritma Pemrograman',
            'schedule_day' => 'Senin',
            'schedule_time' => '08:00 - 10:30',
            'room' => 'Lab 1',
        ]);

        $this->student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $classroom->id,
        ]);

        $this->session = PracticumSession::create([
            'classroom_id' => $classroom->id,
            'created_by' => $admin->id,
            'meeting_number' => 1,
            'title' => 'Pengenalan',
            'session_date' => now()->toDateString(),
            'qr_token' => Str::random(40),
            'status' => 'active',
        ]);

        PretestQuestion::create([
            'practicum_session_id' => $this->session->id,
            'question_text' => 'Apa itu algoritma?',
            'code_language' => 'php',
            'order' => 1,
        ]);

        $this->attendance = Attendance::create([
            'practicum_session_id' => $this->session->id,
            'user_id' => $this->student->id,
            'status' => 'hadir',
            'scanned_at' => now(),
        ]);
    }

    public function test_first_open_sets_a_fifteen_minute_deadline(): void
    {
        $start = now()->setTime(8, 0);
        $this->travelTo($start);

        $this->actingAs($this->student)
            ->get(route('mahasiswa.pretest.show', $this->session))
            ->assertOk()
            ->assertSee('let remaining = 900', false);

        $this->assertTrue(
            $this->attendance->fresh()->pretest_deadline->equalTo($start->copy()->addMinutes(15))
        );
    }

    public function test_refresh_does_not_extend_the_deadline(): void
    {
        $start = now()->setTime(8, 0);
        $this->travelTo($start);

        $this->actingAs($this->student)->get(route('mahasiswa.pretest.show', $this->session));

        $this->travel(5)->minutes();

        $this->actingAs($this->student)
            ->get(route('mahasiswa.pretest.show', $this->session))
            ->assertOk()
            ->assertSee('let remaining = 600', false);

        $this->assertTrue(
            $this->attendance->fresh()->pretest_deadline->equalTo($start->copy()->addMinutes(15))
        );
    }

    public function test_expired_pretest_is_closed_at_the_deadline(): void
    {
        $start = now()->setTime(8, 0);
        $this->travelTo($start);
        $this->actingAs($this->student)->get(route('mahasiswa.pretest.show', $this->session));

        $this->travel(16)->minutes();

        $this->actingAs($this->student)
            ->get(route('mahasiswa.pretest.show', $this->session))
            ->assertViewIs('mahasiswa.pretest-closed');

        $attendance = $this->attendance->fresh();
        $this->assertTrue($attendance->pretest_finished_at->equalTo($attendance->pretest_deadline));
    }

    public function test_locked_attendance_is_redirected_to_dashboard(): void
    {
        $this->attendance->update([
            'pretest_started_at' => now(),
            'pretest_deadline' => now()->addMinutes(10),
            'pretest_locked_at' => now(),
        ]);

        $this->actingAs($this->student)
            ->get(route('mahasiswa.pretest.show', $this->session))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_legacy_attendance_without_deadline_gets_one(): void
    {
        $start = now()->setTime(8, 0);
        $this->attendance->update([
            'pretest_started_at' => $start,
            'pretest_deadline' => null,
        ]);

        $this->travelTo($start->copy()->addMinutes(5));

        $this->actingAs($this->student)
            ->get(route('mahasiswa.pretest.show', $this->session))
            ->assertOk()
            ->assertSee('let remaining = 600', false);

        $this->assertTrue(
            $this->attendance->fresh()->pretest_deadline->equalTo($start->copy()->addMinutes(15))
        );
    }

    public function test_finish_uses_the_deadline_when_time_has_already_passed(): void
    {
        $start = now()->setTime(8, 0);
        $this->attendance->update([
            'pretest_started_at' => $start,
            'pretest_deadline' => $start->copy()->addMinutes(15),
        ]);

        $this->travelTo($start->copy()->addMinutes(20));

        $this->actingAs($this->student)
            ->post(route('mahasiswa.pretest.finish', $this->session))
            ->assertViewIs('mahasiswa.pretest-closed');

        $this->assertTrue(
            $this->attendance->fresh()->pretest_finished_at->equalTo($start->copy()->addMinutes(15))
        );
    }

    public function test_locked_attendance_cannot_finish(): void
    {
        $this->attendance->update([
            'pretest_started_at' => now(),
            'pretest_deadline' => now()->addMinutes(10),
            'pretest_locked_at' => now(),
        ]);

        $this->actingAs($this->student)
            ->post(route('mahasiswa.pretest.finish', $this->session))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_pretest_page_renders_the_anti_cheat_guard(): void
    {
        $this->travelTo(now()->setTime(8, 0));

        $this->actingAs($this->student)
            ->get(route('mahasiswa.pretest.show', $this->session))
            ->assertOk()
            ->assertSee(route('mahasiswa.pretest.violation', $this->session), false)
            ->assertSee(route('mahasiswa.pretest.state', $this->session), false)
            ->assertSee('pretest-violation')
            ->assertSee('pretest-warning')
            ->assertDontSee('return confirm(', false);
    }

    public function test_pretest_index_marks_a_locked_attempt(): void
    {
        $this->attendance->update([
            'pretest_started_at' => now(),
            'pretest_deadline' => now()->addMinutes(10),
            'pretest_locked_at' => now(),
        ]);

        $this->actingAs($this->student)
            ->get(route('mahasiswa.pretest.index'))
            ->assertOk()
            ->assertSee('Dikunci karena pelanggaran');
    }

    public function test_first_violation_cuts_five_minutes(): void
    {
        $start = now()->setTime(8, 0);
        $this->travelTo($start);
        $this->attendance->update([
            'pretest_started_at' => $start,
            'pretest_deadline' => $start->copy()->addMinutes(15),
        ]);

        $response = $this->actingAs($this->student)
            ->postJson(route('mahasiswa.pretest.violation', $this->session))
            ->assertOk()
            ->assertJsonPath('violations', 1)
            ->assertJsonPath('locked', false)
            ->assertJsonPath('finished', false);

        $this->assertSame(600, $response->json('remaining_seconds'));
        $this->assertTrue(
            $this->attendance->fresh()->pretest_deadline->equalTo($start->copy()->addMinutes(10))
        );
    }

    public function test_second_violation_locks_access(): void
    {
        $start = now()->setTime(8, 0);
        $this->travelTo($start);
        $this->attendance->update([
            'pretest_started_at' => $start,
            'pretest_deadline' => $start->copy()->addMinutes(15),
        ]);

        $this->actingAs($this->student)->postJson(route('mahasiswa.pretest.violation', $this->session));

        $this->travel(6)->seconds();

        $this->actingAs($this->student)
            ->postJson(route('mahasiswa.pretest.violation', $this->session))
            ->assertOk()
            ->assertJsonPath('violations', 2)
            ->assertJsonPath('locked', true);

        $this->assertNotNull($this->attendance->fresh()->pretest_locked_at);

        $this->actingAs($this->student)
            ->get(route('mahasiswa.pretest.show', $this->session))
            ->assertRedirect(route('dashboard'));
    }

    public function test_violation_within_cooldown_is_ignored(): void
    {
        $start = now()->setTime(8, 0);
        $this->travelTo($start);
        $this->attendance->update([
            'pretest_started_at' => $start,
            'pretest_deadline' => $start->copy()->addMinutes(15),
        ]);

        $this->actingAs($this->student)->postJson(route('mahasiswa.pretest.violation', $this->session));

        $this->actingAs($this->student)
            ->postJson(route('mahasiswa.pretest.violation', $this->session))
            ->assertOk()
            ->assertJsonPath('violations', 1)
            ->assertJsonPath('ignored', true);

        $this->assertTrue(
            $this->attendance->fresh()->pretest_deadline->equalTo($start->copy()->addMinutes(10))
        );
    }

    public function test_penalty_past_the_deadline_finishes_the_pretest(): void
    {
        $start = now()->setTime(8, 0);
        $this->travelTo($start);
        $this->attendance->update([
            'pretest_started_at' => $start,
            'pretest_deadline' => $start->copy()->addMinutes(2),
        ]);

        $this->actingAs($this->student)
            ->postJson(route('mahasiswa.pretest.violation', $this->session))
            ->assertOk()
            ->assertJsonPath('violations', 1)
            ->assertJsonPath('finished', true)
            ->assertJsonPath('remaining_seconds', 0);

        $this->assertNotNull($this->attendance->fresh()->pretest_finished_at);
    }

    public function test_state_returns_the_assistant_warning(): void
    {
        $this->attendance->update([
            'pretest_started_at' => now(),
            'pretest_deadline' => now()->addMinutes(10),
            'pretest_warning' => 'Fokus ke layar Anda.',
            'pretest_warning_at' => now(),
        ]);

        $this->actingAs($this->student)
            ->getJson(route('mahasiswa.pretest.state', $this->session))
            ->assertOk()
            ->assertJsonPath('warning.message', 'Fokus ke layar Anda.')
            ->assertJsonPath('locked', false);
    }

    public function test_finished_attendance_cannot_add_violations(): void
    {
        $this->attendance->update([
            'pretest_started_at' => now(),
            'pretest_deadline' => now()->addMinutes(10),
            'pretest_finished_at' => now(),
        ]);

        $this->actingAs($this->student)
            ->postJson(route('mahasiswa.pretest.violation', $this->session))
            ->assertOk()
            ->assertJsonPath('violations', 0)
            ->assertJsonPath('finished', true);
    }

    public function test_student_from_another_class_cannot_report_a_violation(): void
    {
        $other = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => Classroom::create([
                'code' => 'ALPRO-B',
                'name' => 'ALPRO B',
                'course_name' => 'Algoritma Pemrograman',
                'schedule_day' => 'Selasa',
                'schedule_time' => '08:00 - 10:30',
                'room' => 'Lab 2',
            ])->id,
        ]);

        $this->actingAs($other)
            ->postJson(route('mahasiswa.pretest.violation', $this->session))
            ->assertForbidden();
    }
}
