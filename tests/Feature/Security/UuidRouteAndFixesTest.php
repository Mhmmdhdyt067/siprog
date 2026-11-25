<?php

namespace Tests\Feature\Security;

use App\Enums\UserRole;
use App\Exports\SessionGradesExport;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\PracticumSession;
use App\Models\PretestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Tests\TestCase;

class UuidRouteAndFixesTest extends TestCase
{
    use RefreshDatabase;

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

    private function makeSession(Classroom $classroom, User $creator, string $status = 'active', int $meeting = 1): PracticumSession
    {
        return PracticumSession::create([
            'classroom_id' => $classroom->id,
            'created_by' => $creator->id,
            'meeting_number' => $meeting,
            'title' => 'Pengenalan',
            'session_date' => now()->toDateString(),
            'qr_token' => Str::random(40),
            'qr_expires_at' => now()->addHours(3),
            'status' => $status,
        ]);
    }

    private function assignment(PracticumSession $session, User $creator): Assignment
    {
        return Assignment::create([
            'practicum_session_id' => $session->id,
            'created_by' => $creator->id,
            'title' => 'Tugas 1',
            'file_path' => 'assignments/soal.pdf',
            'deadline' => now()->addWeek(),
        ]);
    }

    public function test_all_routable_models_use_uuid_as_route_key(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = $this->classroom('ALPRO-A');
        $session = $this->makeSession($classroom, $admin);
        $assignment = $this->assignment($session, $admin);
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $classroom->id]);
        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'file_path' => 'submissions/jawaban.pdf',
            'submitted_at' => now(),
        ]);
        $attendance = Attendance::create([
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'status' => 'hadir',
            'scanned_at' => now(),
        ]);
        $question = PretestQuestion::create([
            'practicum_session_id' => $session->id,
            'question_text' => 'Apa itu variabel?',
            'code_language' => 'php',
            'order' => 1,
        ]);
        $announcement = Announcement::create([
            'title' => 'Info',
            'content' => 'Isi',
            'type' => 'info',
            'created_by' => $admin->id,
            'is_published' => true,
        ]);

        foreach ([$admin, $classroom, $session, $assignment, $submission, $attendance, $question, $announcement] as $model) {
            $this->assertNotEmpty($model->uuid);
            $this->assertSame('uuid', $model->getRouteKeyName());
        }

        $this->assertStringContainsString($session->uuid, route('sessions.qr', $session));
        $this->assertStringNotContainsString('/sessions/'.$session->id.'/', route('sessions.qr', $session));
    }

    public function test_numeric_id_in_url_no_longer_resolves(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $session = $this->makeSession($this->classroom('ALPRO-A'), $admin);

        $this->actingAs($admin)
            ->get('/sessions/'.$session->id.'/qr')
            ->assertNotFound();

        $this->actingAs($admin)
            ->get(route('sessions.qr', $session))
            ->assertOk();
    }

    public function test_finished_session_qr_cannot_reopen_attendance(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $session = $this->makeSession($this->classroom('ALPRO-A'), $admin, 'finished');

        $this->actingAs($admin)
            ->get(route('sessions.qr', $session))
            ->assertRedirect(route('sessions.index'));

        $this->actingAs($admin)
            ->postJson(route('sessions.qr.refresh', $session))
            ->assertStatus(422);

        $this->assertSame('finished', $session->fresh()->status);
    }

    public function test_deleting_assistant_preserves_their_sessions_and_assignments(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $assistant = User::factory()->create(['role' => UserRole::ASISTEN]);
        $classroom = $this->classroom('ALPRO-A');
        $session = $this->makeSession($classroom, $assistant);
        $assignment = $this->assignment($session, $assistant);

        $this->actingAs($admin)
            ->delete(route('admin.master.users.destroy', $assistant))
            ->assertRedirect();

        $this->assertDatabaseHas('practicum_sessions', ['id' => $session->id, 'created_by' => null]);
        $this->assertDatabaseHas('assignments', ['id' => $assignment->id, 'created_by' => null]);
    }

    public function test_deleting_student_removes_their_submission_files(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = $this->classroom('ALPRO-A');
        $session = $this->makeSession($classroom, $admin);
        $assignment = $this->assignment($session, $admin);
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $classroom->id]);

        Storage::disk('local')->put('submissions/jawaban.pdf', 'pdf');
        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'file_path' => 'submissions/jawaban.pdf',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.master.users.destroy', $student))
            ->assertRedirect();

        Storage::disk('local')->assertMissing('submissions/jawaban.pdf');
    }

    public function test_graduation_only_counts_finished_sessions(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = $this->classroom('ALPRO-A');
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $classroom->id]);

        $finished = $this->makeSession($classroom, $admin, 'finished');
        $active = $this->makeSession($classroom, $admin, 'active', 2);

        foreach ([$finished, $active] as $session) {
            Attendance::create([
                'practicum_session_id' => $session->id,
                'user_id' => $student->id,
                'status' => 'hadir',
                'scanned_at' => now(),
            ]);
        }

        Grade::create([
            'practicum_session_id' => $finished->id,
            'user_id' => $student->id,
            'total_score' => 80,
        ]);
        Grade::create([
            'practicum_session_id' => $active->id,
            'user_id' => $student->id,
            'total_score' => 100,
        ]);

        $status = $student->fresh()->graduation_status;

        $this->assertSame(1, $status['attended_count']);
        $this->assertSame(1, $status['total_sessions']);
        $this->assertSame(100.0, $status['attendance_percentage']);
        $this->assertSame(80.0, $status['average_score']);
    }

    public function test_feedback_requires_attendance(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = $this->classroom('ALPRO-A');
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $classroom->id]);
        $session = $this->makeSession($classroom, $admin, 'finished');

        $this->actingAs($student)
            ->post(route('mahasiswa.feedback.store', $session), ['rating' => 5, 'comment' => 'Bagus'])
            ->assertForbidden();

        Attendance::create([
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'status' => 'hadir',
            'scanned_at' => now(),
        ]);

        $this->actingAs($student)
            ->post(route('mahasiswa.feedback.store', $session), ['rating' => 5, 'comment' => 'Bagus'])
            ->assertRedirect();

        $this->assertDatabaseCount('feedbacks', 1);
    }

    public function test_session_cannot_move_classroom_after_attendance_exists(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $own = $this->classroom('ALPRO-A');
        $other = $this->classroom('ALPRO-B');
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $own->id]);
        $session = $this->makeSession($own, $admin);

        Attendance::create([
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'status' => 'hadir',
            'scanned_at' => now(),
        ]);

        $this->actingAs($admin)
            ->putJson(route('sessions.update', $session), [
                'classroom_id' => $other->id,
                'meeting_number' => 1,
                'title' => 'Pindah',
                'session_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('classroom_id');

        $this->assertSame($own->id, $session->fresh()->classroom_id);
    }

    public function test_assignment_cannot_move_across_classrooms_after_submission_exists(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $own = $this->classroom('ALPRO-A');
        $other = $this->classroom('ALPRO-B');
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $own->id]);
        $session = $this->makeSession($own, $admin);
        $otherSession = $this->makeSession($other, $admin);
        $assignment = $this->assignment($session, $admin);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'file_path' => 'submissions/jawaban.pdf',
            'submitted_at' => now(),
        ]);

        $this->actingAs($admin)
            ->putJson(route('admin.assignments.update', $assignment), [
                'practicum_session_id' => $otherSession->id,
                'title' => 'Tugas 1',
                'deadline' => now()->addWeek()->format('Y-m-d H:i:s'),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('practicum_session_id');

        $this->assertSame($session->id, $assignment->fresh()->practicum_session_id);
    }

    public function test_export_treats_user_strings_as_text_not_formulas(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $export = new SessionGradesExport($this->makeSession($this->classroom('ALPRO-A'), $admin));

        $cell = (new Worksheet)->getCell('A1');
        $export->bindValue($cell, '=HYPERLINK("http://example.test","klik")');

        $this->assertSame(DataType::TYPE_STRING, $cell->getDataType());
        $this->assertSame('=HYPERLINK("http://example.test","klik")', $cell->getValue());
    }

    public function test_duplicate_meeting_number_is_a_validation_error(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = $this->classroom('ALPRO-A');
        $this->makeSession($classroom, $admin);

        $this->actingAs($admin)
            ->postJson(route('sessions.store'), [
                'classroom_id' => $classroom->id,
                'meeting_number' => 1,
                'title' => 'Duplikat',
                'session_date' => now()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('meeting_number');
    }
}
