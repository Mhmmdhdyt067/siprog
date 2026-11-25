<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AssignmentStoreTest extends TestCase
{
    use RefreshDatabase;

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

        $session = PracticumSession::create([
            'classroom_id' => $classroom->id,
            'created_by' => $admin->id,
            'meeting_number' => 1,
            'title' => 'Pengenalan',
            'session_date' => now()->toDateString(),
            'qr_token' => Str::random(40),
            'status' => 'active',
        ]);

        return [$admin, $session];
    }

    private function payload(PracticumSession $session): array
    {
        return [
            'practicum_session_id' => $session->id,
            'title' => 'Tugas Pertemuan 1',
            'description' => 'Kerjakan soal.',
            'file_soal' => UploadedFile::fake()->create('soal.pdf', 100, 'application/pdf'),
            'deadline' => now()->addWeek()->format('Y-m-d H:i:s'),
        ];
    }

    public function test_one_session_can_only_have_one_assignment(): void
    {
        Storage::fake('local');

        [$admin, $session] = $this->context();

        Assignment::create([
            'practicum_session_id' => $session->id,
            'created_by' => $admin->id,
            'title' => 'Tugas Lama',
            'file_path' => 'assignments/lama.pdf',
            'deadline' => now()->addWeek(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.assignments.store'), $this->payload($session))
            ->assertSessionHasErrors('practicum_session_id');

        $this->assertSame(1, Assignment::where('practicum_session_id', $session->id)->count());
    }

    public function test_creating_assignment_also_creates_the_announcement(): void
    {
        Storage::fake('local');

        [$admin, $session] = $this->context();

        $this->actingAs($admin)
            ->post(route('admin.assignments.store'), $this->payload($session))
            ->assertRedirect();

        $this->assertDatabaseHas('assignments', ['practicum_session_id' => $session->id]);
        $this->assertDatabaseHas('announcements', ['classroom_id' => $session->classroom_id, 'type' => 'tugas']);
    }
}
