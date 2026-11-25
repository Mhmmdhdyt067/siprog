<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AssignmentReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_submission_review_index(): void
    {
        [$admin, $assignment] = $this->scenario();

        $this->actingAs($admin)
            ->get(route('admin.submissions.index'))
            ->assertOk()
            ->assertSee('Periksa Tugas Mingguan')
            ->assertSee($assignment->title);
    }

    public function test_admin_can_view_assignment_submission_detail(): void
    {
        [$admin, $assignment, $student, $submission] = $this->scenario(withSubmission: true);

        $this->actingAs($admin)
            ->get(route('admin.submissions.show', $assignment))
            ->assertOk()
            ->assertSee($student->name)
            ->assertSee($student->nim_nip)
            ->assertSee($submission->original_filename)
            ->assertSee('Pratinjau PDF Pengumpulan');
    }

    public function test_admin_can_grade_a_submitted_assignment(): void
    {
        [$admin, $assignment, $student] = $this->scenario(withSubmission: true);

        $session = $assignment->session;

        Grade::create([
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'graded_by' => $admin->id,
            'pretest_score' => 80,
            'assignment_score' => 50,
            'extra_score' => 5,
            'total_score' => 67,
            'notes' => 'Pertahankan kualitas jawaban.',
        ]);

        $response = $this->actingAs($admin)->postJson(
            route('admin.submissions.grade', [$assignment, $student]),
            ['assignment_score' => 90]
        );

        $response->assertOk()
            ->assertJsonPath('message', "Nilai tugas {$student->name} berhasil disimpan.")
            ->assertJsonStructure(['message', 'html']);

        $this->assertDatabaseHas('grades', [
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'graded_by' => $admin->id,
            'pretest_score' => 80,
            'assignment_score' => 90,
            'extra_score' => 5,
            'total_score' => 91,
            'notes' => 'Pertahankan kualitas jawaban.',
        ]);
    }

    public function test_assignment_cannot_be_graded_without_a_submission(): void
    {
        [$admin, $assignment, $student] = $this->scenario();

        $response = $this->actingAs($admin)->postJson(
            route('admin.submissions.grade', [$assignment, $student]),
            ['assignment_score' => 90]
        );

        $response->assertUnprocessable()->assertJsonValidationErrors('assignment_score');

        $this->assertDatabaseMissing('grades', [
            'practicum_session_id' => $assignment->practicum_session_id,
            'user_id' => $student->id,
        ]);
    }

    public function test_student_outside_the_classroom_cannot_be_graded(): void
    {
        [$admin, $assignment] = $this->scenario();

        $outsider = User::factory()->create(['role' => UserRole::MAHASISWA]);

        $this->actingAs($admin)
            ->postJson(route('admin.submissions.grade', [$assignment, $outsider]), ['assignment_score' => 90])
            ->assertNotFound();
    }

    public function test_non_admin_cannot_access_submission_review_pages(): void
    {
        [, $assignment, $student] = $this->scenario(withSubmission: true);

        $this->actingAs($student)
            ->get(route('admin.submissions.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('admin.submissions.show', $assignment))
            ->assertForbidden();
    }

    /**
     * @return array{0: User, 1: Assignment, 2: User, 3: AssignmentSubmission}
     */
    private function scenario(bool $withSubmission = false): array
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $classroom = Classroom::create([
            'code' => 'ALPRO-A',
            'name' => 'ALPRO A',
            'course_name' => 'Algoritma Pemrograman',
            'schedule_day' => 'Senin',
            'schedule_time' => '08:00 - 10:30',
            'room' => 'Lab Komputer 1',
        ]);

        $student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $classroom->id,
        ]);

        $session = PracticumSession::create([
            'classroom_id' => $classroom->id,
            'created_by' => $admin->id,
            'meeting_number' => 1,
            'title' => 'Pengenalan Algoritma',
            'description' => 'Sesi pertama praktikum.',
            'session_date' => now()->toDateString(),
            'qr_token' => Str::random(32),
            'status' => 'finished',
        ]);

        $assignment = Assignment::create([
            'practicum_session_id' => $session->id,
            'created_by' => $admin->id,
            'title' => 'Tugas Pertemuan 1',
            'description' => 'Kerjakan soal yang terlampir.',
            'file_path' => 'assignments/soal-pertemuan-1.pdf',
            'deadline' => now()->addWeek(),
        ]);

        $submission = new AssignmentSubmission;

        if ($withSubmission) {
            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => $student->id,
                'file_path' => 'submissions/jawaban-pertemuan-1.pdf',
                'original_filename' => 'jawaban-pertemuan-1.pdf',
                'submitted_at' => now(),
            ]);
        }

        return [$admin, $assignment, $student, $submission];
    }
}
