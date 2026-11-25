<?php

namespace Tests\Feature\Mahasiswa;

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

class NilaiPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_grade_transparency_page(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $classroom = Classroom::create([
            'code' => 'ALPRO-B',
            'name' => 'ALPRO B',
            'course_name' => 'Algoritma Pemrograman',
            'schedule_day' => 'Selasa',
            'schedule_time' => '13:00 - 15:30',
            'room' => 'Lab Komputer 2',
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

        Grade::create([
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
            'graded_by' => $admin->id,
            'pretest_score' => 80,
            'assignment_score' => 90,
            'extra_score' => 5,
            'total_score' => 91,
            'notes' => 'Jawaban sangat rapi.',
        ]);

        $assignment = Assignment::create([
            'practicum_session_id' => $session->id,
            'created_by' => $admin->id,
            'title' => 'Tugas Pertemuan 1',
            'description' => 'Kerjakan soal yang terlampir.',
            'file_path' => 'assignments/soal-pertemuan-1.pdf',
            'deadline' => now()->addWeek(),
        ]);

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $student->id,
            'file_path' => 'submissions/jawaban-pertemuan-1.pdf',
            'original_filename' => 'jawaban-pertemuan-1.pdf',
            'submitted_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('mahasiswa.nilai.index'))
            ->assertOk()
            ->assertSee('Transparansi Nilai Praktikum')
            ->assertSee('Pengenalan Algoritma')
            ->assertSee('91.00')
            ->assertSee('Jawaban sangat rapi.')
            ->assertSee('PDF terkumpul');
    }
}
