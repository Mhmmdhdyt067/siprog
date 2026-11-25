<?php

namespace Tests\Feature\Asisten;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\PretestQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AssistantContentAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $assistant;
    private Classroom $own;
    private Classroom $other;
    private PracticumSession $ownSession;
    private PracticumSession $otherSession;
    private Assignment $ownAssignment;
    private Assignment $otherAssignment;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->assistant = User::factory()->create(['role' => UserRole::ASISTEN]);
        $this->own = $this->classroom('ALPRO-A');
        $this->other = $this->classroom('ALPRO-B');
        $this->assistant->assistedClassrooms()->attach($this->own->id);

        $this->ownSession = $this->makeSession($this->own, 1, 'Pertemuan Milik Asisten');
        $this->otherSession = $this->makeSession($this->other, 2, 'Pertemuan Kelas Lain');
        $this->ownAssignment = $this->assignment($this->ownSession, 'Tugas Milik Asisten');
        $this->otherAssignment = $this->assignment($this->otherSession, 'Tugas Kelas Lain');
    }

    public function test_assistant_only_sees_assignments_from_assisted_classrooms(): void
    {
        $this->actingAs($this->assistant)
            ->get(route('admin.assignments.index'))
            ->assertOk()
            ->assertSee($this->ownAssignment->title)
            ->assertDontSee($this->otherAssignment->title);
    }

    public function test_assistant_cannot_create_assignment_for_another_classroom(): void
    {
        $this->actingAs($this->assistant)
            ->post(route('admin.assignments.store'), $this->assignmentPayload($this->otherSession))
            ->assertForbidden();

        $this->assertDatabaseMissing('assignments', ['title' => 'Tugas Baru']);
    }

    public function test_assistant_cannot_move_assignment_to_another_classroom(): void
    {
        // Update tidak mengirim file: multipart pada PUT tidak diparse PHP,
        // dan fokus test ini adalah otorisasi sesi tujuan.
        $this->actingAs($this->assistant)
            ->put(route('admin.assignments.update', $this->ownAssignment), [
                'practicum_session_id' => $this->otherSession->id,
                'title' => 'Tugas Baru',
                'description' => 'Deskripsi tugas',
                'deadline' => now()->addWeek()->format('Y-m-d H:i:s'),
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('assignments', [
            'id' => $this->ownAssignment->id,
            'practicum_session_id' => $this->ownSession->id,
        ]);
    }

    public function test_assistant_can_manage_pretest_questions_only_for_assisted_classrooms(): void
    {
        $payload = [
            'question_text' => 'Apa itu algoritma?',
            'code_language' => 'php',
            'order' => 1,
        ];

        $this->actingAs($this->assistant)
            ->postJson(route('admin.pretest.store', $this->ownSession), $payload)
            ->assertOk();

        $this->assertDatabaseHas('pretest_questions', [
            'practicum_session_id' => $this->ownSession->id,
            'question_text' => 'Apa itu algoritma?',
        ]);

        $this->actingAs($this->assistant)
            ->postJson(route('admin.pretest.store', $this->otherSession), $payload)
            ->assertForbidden();
    }

    public function test_assistant_cannot_edit_or_delete_questions_from_another_classroom(): void
    {
        $question = PretestQuestion::create([
            'practicum_session_id' => $this->otherSession->id,
            'question_text' => 'Soal rahasia',
            'code_language' => 'php',
            'order' => 1,
        ]);

        $this->actingAs($this->assistant)
            ->putJson(route('admin.pretest.update', $question), [
                'question_text' => 'Diubah',
                'code_language' => 'php',
                'order' => 1,
            ])
            ->assertForbidden();

        $this->actingAs($this->assistant)
            ->deleteJson(route('admin.pretest.destroy', $question))
            ->assertForbidden();
    }

    public function test_assistant_can_grade_only_submissions_from_assisted_classrooms(): void
    {
        $student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $this->own->id,
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $this->ownAssignment->id,
            'user_id' => $student->id,
            'file_path' => 'submissions/jawaban.pdf',
            'original_filename' => 'jawaban.pdf',
            'submitted_at' => now(),
        ]);

        $this->actingAs($this->assistant)
            ->postJson(route('admin.submissions.grade', [$this->ownAssignment, $student]), [
                'assignment_score' => 90,
            ])
            ->assertOk();

        $this->assertDatabaseHas('grades', [
            'practicum_session_id' => $this->ownSession->id,
            'user_id' => $student->id,
            'assignment_score' => 90,
        ]);
    }

    public function test_assistant_cannot_review_or_grade_another_classroom(): void
    {
        $otherStudent = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $this->other->id,
        ]);

        $this->actingAs($this->assistant)
            ->get(route('admin.submissions.show', $this->otherAssignment))
            ->assertForbidden();

        $this->actingAs($this->assistant)
            ->postJson(route('admin.submissions.grade', [$this->otherAssignment, $otherStudent]), [
                'assignment_score' => 90,
            ])
            ->assertForbidden();
    }

    public function test_assistant_submission_index_only_lists_assisted_classrooms(): void
    {
        $this->actingAs($this->assistant)
            ->get(route('admin.submissions.index'))
            ->assertOk()
            ->assertSee($this->ownAssignment->title)
            ->assertDontSee($this->otherAssignment->title);
    }

    public function test_student_cannot_access_assistant_content_routes(): void
    {
        $student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $this->own->id,
        ]);

        foreach ([
            route('admin.assignments.index'),
            route('admin.submissions.index'),
            route('admin.pretest.index', $this->ownSession),
        ] as $url) {
            $this->actingAs($student)->get($url)->assertForbidden();
        }
    }

    public function test_assistant_overview_lists_only_assisted_sessions(): void
    {
        $this->actingAs($this->assistant)
            ->get(route('admin.pretest.overview'))
            ->assertOk()
            ->assertSee($this->ownSession->title)
            ->assertDontSee($this->otherSession->title);
    }

    public function test_assistant_sees_pretest_link_on_session_list(): void
    {
        $this->actingAs($this->assistant)
            ->get(route('sessions.index'))
            ->assertOk()
            ->assertSee(route('admin.pretest.index', $this->ownSession), false);
    }

    public function test_assistant_navigation_links_to_content_and_monitoring(): void
    {
        $this->actingAs($this->assistant)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('admin.pretest.overview'), false)
            ->assertSee(route('admin.assignments.index'), false)
            ->assertSee(route('admin.submissions.index'), false)
            ->assertSee(route('admin.pretest.monitor'), false);
    }

    public function test_student_navigation_hides_assistant_links(): void
    {
        $student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'classroom_id' => $this->own->id,
        ]);

        $this->actingAs($student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('admin.assignments.index'), false)
            ->assertDontSee(route('admin.pretest.monitor'), false);
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

    private function makeSession(Classroom $classroom, int $meeting, string $title): PracticumSession
    {
        return PracticumSession::create([
            'classroom_id' => $classroom->id,
            'created_by' => $this->admin->id,
            'meeting_number' => $meeting,
            'title' => $title,
            'session_date' => now()->toDateString(),
            'qr_token' => Str::random(40),
            'status' => 'active',
        ]);
    }

    private function assignment(PracticumSession $session, string $title): Assignment
    {
        return Assignment::create([
            'practicum_session_id' => $session->id,
            'created_by' => $this->admin->id,
            'title' => $title,
            'file_path' => 'assignments/soal.pdf',
            'deadline' => now()->addWeek(),
        ]);
    }

    private function assignmentPayload(PracticumSession $session): array
    {
        return [
            'practicum_session_id' => $session->id,
            'title' => 'Tugas Baru',
            'description' => 'Deskripsi tugas',
            'deadline' => now()->addWeek()->format('Y-m-d H:i:s'),
            'file_soal' => UploadedFile::fake()->create('soal.pdf', 100, 'application/pdf'),
        ];
    }
}
