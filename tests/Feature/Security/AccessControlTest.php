<?php

namespace Tests\Feature\Security;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Guards the authorization fixes made before deployment: assistants must stay
 * inside the classrooms they assist, and students only inside their own class.
 */
class AccessControlTest extends TestCase
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

    private function makeSession(Classroom $classroom, User $creator): PracticumSession
    {
        return PracticumSession::create([
            'classroom_id' => $classroom->id,
            'created_by' => $creator->id,
            'meeting_number' => 1,
            'title' => 'Pengenalan',
            'session_date' => now()->toDateString(),
            'qr_token' => Str::random(40),
            'status' => 'active',
        ]);
    }

    public function test_assistant_cannot_open_grading_of_another_classroom(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $own = $this->classroom('ALPRO-A');
        $other = $this->classroom('ALPRO-B');

        $assistant = User::factory()->create(['role' => UserRole::ASISTEN]);
        $assistant->assistedClassrooms()->attach($own->id);

        $session = $this->makeSession($other, $admin);

        $this->actingAs($assistant)
            ->get(route('grading.index', $session))
            ->assertForbidden();
    }

    public function test_assistant_cannot_download_submission_from_another_classroom(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $own = $this->classroom('ALPRO-A');
        $other = $this->classroom('ALPRO-B');

        $assistant = User::factory()->create(['role' => UserRole::ASISTEN]);
        $assistant->assistedClassrooms()->attach($own->id);

        $session = $this->makeSession($other, $admin);
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $other->id]);

        $assignment = Assignment::create([
            'practicum_session_id' => $session->id,
            'created_by' => $admin->id,
            'title' => 'Tugas 1',
            'file_path' => 'assignments/soal.pdf',
            'deadline' => now()->addWeek(),
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'file_path' => 'submissions/jawaban.pdf',
            'submitted_at' => now(),
        ]);

        $this->actingAs($assistant)
            ->get(route('submissions.view', $submission))
            ->assertForbidden();
    }

    public function test_student_cannot_submit_after_the_deadline(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = $this->classroom('ALPRO-A');
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $classroom->id]);

        $session = $this->makeSession($classroom, $admin);

        $assignment = Assignment::create([
            'practicum_session_id' => $session->id,
            'created_by' => $admin->id,
            'title' => 'Tugas 1',
            'file_path' => 'assignments/soal.pdf',
            'deadline' => now()->subDay(),
        ]);

        $this->actingAs($student)
            ->postJson(route('mahasiswa.tugas.submit', $assignment), [
                'file_pdf' => UploadedFile::fake()->create('jawaban.pdf', 100, 'application/pdf'),
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('assignment_submissions', 0);
    }

    public function test_student_cannot_send_feedback_for_another_classroom(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = $this->classroom('ALPRO-A');
        $other = $this->classroom('ALPRO-B');
        $student = User::factory()->create(['role' => UserRole::MAHASISWA, 'classroom_id' => $classroom->id]);

        $session = $this->makeSession($other, $admin);
        $session->update(['status' => 'finished']);

        $this->actingAs($student)
            ->post(route('mahasiswa.feedback.store', $session), [
                'rating' => 5,
                'comment' => 'Mantap',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('feedbacks', 0);
    }

    public function test_guest_cannot_download_assignment_file(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $classroom = $this->classroom('ALPRO-A');
        $session = $this->makeSession($classroom, $admin);

        $assignment = Assignment::create([
            'practicum_session_id' => $session->id,
            'created_by' => $admin->id,
            'title' => 'Tugas 1',
            'file_path' => 'assignments/soal.pdf',
            'deadline' => now()->addWeek(),
        ]);

        $this->get(route('assignments.file', $assignment))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->actingAs($admin)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $this->actingAs($admin)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertDontSee('Zona Berbahaya');
    }
}
