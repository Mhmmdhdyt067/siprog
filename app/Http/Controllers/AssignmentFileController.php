<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Assignment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignmentFileController extends Controller
{
    /**
     * Serve the assignment file to any signed-in user who may see it.
     * Files live on the private disk, so the URL is never public.
     */
    public function __invoke(Assignment $assignment)
    {
        $user = auth()->user();
        $classroomId = $assignment->session->classroom_id;

        $allowed = match ($user->role) {
            UserRole::ADMIN => true,
            UserRole::ASISTEN => $user->assistedClassrooms()
                ->where('classrooms.id', $classroomId)
                ->exists(),
            default => $user->classroom_id === $classroomId,
        };

        abort_unless($allowed, 403);

        abort_unless(
            Storage::disk('local')->exists($assignment->file_path),
            404,
            'File tugas belum tersedia di server.'
        );

        $ext = pathinfo($assignment->file_path, PATHINFO_EXTENSION) ?: 'pdf';
        $name = Str::slug($assignment->title).'-pertemuan-'.$assignment->session->meeting_number.'.'.$ext;

        return Storage::disk('local')->download($assignment->file_path, $name);
    }
}
