<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TugasController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $assignments = Assignment::with([
            'session',
            'submissions' => fn ($q) => $q->where('user_id', $user->id),
        ])
            ->whereHas('session', fn ($q) => $q->where('classroom_id', $user->classroom_id))
            ->latest()
            ->get();

        return view('mahasiswa.tugas', compact('assignments'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        abort_if($assignment->session->classroom_id !== auth()->user()->classroom_id, 403);

        if ($assignment->deadline->isPast()) {
            throw ValidationException::withMessages([
                'file_pdf' => 'Batas waktu pengumpulan tugas ini sudah lewat.',
            ]);
        }

        $request->validate([
            'file_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'], // Wajib PDF maks 10MB
        ], [
            'file_pdf.mimes' => 'File jawaban hasil scan wajib berformat PDF.',
        ]);

        $user = auth()->user();

        // Hapus file lama jika mahasiswa mengunggah ulang revisi
        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->first();

        $file = $request->file('file_pdf');
        $path = $file->store("submissions/assignment_{$assignment->id}");

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $user->id,
            ],
            [
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'submitted_at' => now(),
            ]
        );

        // Only delete the old file after the new one is safely stored.
        if ($existing && $existing->file_path !== $submission->file_path) {
            Storage::disk('local')->delete($existing->file_path);
        }

        return back()->with('success', 'File tugas PDF berhasil diunggah!');
    }
}
