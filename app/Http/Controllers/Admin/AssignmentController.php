<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\PracticumSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function index()
    {
        return view('admin.assignments.index', $this->indexData());
    }

    public function store(Request $request)
    {
        // Otorisasi dulu, baru validasi isi: kesalahan validasi tidak boleh
        // membocorkan data kelas lain ke pengguna tanpa hak akses.
        $request->validate([
            'practicum_session_id' => ['required', 'exists:practicum_sessions,id'],
        ]);

        $session = PracticumSession::findOrFail($request->integer('practicum_session_id'));
        $this->authorizeManagedSession($session);

        $validated = $request->validate([
            'practicum_session_id' => [
                'required',
                Rule::unique('assignments', 'practicum_session_id'),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file_soal' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'deadline' => ['required', 'date'],
        ], [
            'practicum_session_id.unique' => 'Pertemuan ini sudah memiliki tugas. Edit tugas yang sudah ada.',
        ]);

        $path = $request->file('file_soal')->store('assignments');

        DB::transaction(function () use ($validated, $session, $path) {
            Assignment::create([
                'practicum_session_id' => $session->id,
                'created_by' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'file_path' => $path,
                'deadline' => $validated['deadline'],
            ]);

            Announcement::create([
                'title' => "Tugas Mingguan Baru: {$validated['title']}",
                'content' => "Tugas untuk Pertemuan {$session->meeting_number} ({$session->title}) telah diunggah. Silakan unduh pada menu Tugas dan kumpulkan dalam format PDF sebelum ".date('d M Y H:i', strtotime($validated['deadline'])),
                'type' => 'tugas',
                'classroom_id' => $session->classroom_id,
                'created_by' => auth()->id(),
                'is_published' => true,
            ]);
        });

        return $this->respond($request, 'Tugas mingguan berhasil diunggah dan pemberitahuan dikirim.');
    }

    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'practicum_session_id' => ['required', 'exists:practicum_sessions,id'],
        ]);

        $this->authorizeManagedSession($assignment->session);
        $targetSession = PracticumSession::findOrFail($request->integer('practicum_session_id'));
        $this->authorizeManagedSession($targetSession);

        $validated = $request->validate([
            'practicum_session_id' => [
                'required',
                Rule::unique('assignments', 'practicum_session_id')->ignore($assignment->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file_soal' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'deadline' => ['required', 'date'],
        ], [
            'practicum_session_id.unique' => 'Pertemuan ini sudah memiliki tugas. Edit tugas yang sudah ada.',
        ]);

        if ($targetSession->classroom_id !== $assignment->session->classroom_id
            && $assignment->submissions()->exists()) {
            throw ValidationException::withMessages([
                'practicum_session_id' => 'Tugas tidak dapat dipindah ke kelas lain karena sudah ada jawaban mahasiswa.',
            ]);
        }

        $data = [
            'practicum_session_id' => $validated['practicum_session_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'deadline' => $validated['deadline'],
        ];

        if ($request->hasFile('file_soal')) {
            $old = $assignment->file_path;
            $data['file_path'] = $request->file('file_soal')->store('assignments');
            Storage::disk('local')->delete($old);
        }

        $assignment->update($data);

        return $this->respond($request, 'Tugas mingguan berhasil diperbarui.');
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        $this->authorizeManagedSession($assignment->session);

        $files = [$assignment->file_path];

        foreach ($assignment->submissions as $submission) {
            $files[] = $submission->file_path;
        }

        $assignment->delete();
        Storage::disk('local')->delete(array_filter($files));

        return $this->respond($request, 'Tugas mingguan berhasil dihapus.');
    }

    private function indexData(): array
    {
        $classroomIds = $this->managedClassroomIds();

        $assignments = Assignment::with('session.classroom')
            ->whereHas('session', fn ($q) => $q->whereIn('classroom_id', $classroomIds))
            ->withCount('submissions')
            ->latest()
            ->paginate(12);

        $sessions = PracticumSession::with('classroom')
            ->whereIn('classroom_id', $classroomIds)
            ->latest('session_date')
            ->get();

        $now = now();

        $base = Assignment::query()
            ->whereHas('session', fn ($q) => $q->whereIn('classroom_id', $classroomIds));

        $stats = [
            'total' => (clone $base)->count(),
            'open' => (clone $base)->where('deadline', '>=', $now)->count(),
            'closed' => (clone $base)->where('deadline', '<', $now)->count(),
            'submissions' => AssignmentSubmission::whereIn('assignment_id', (clone $base)->select('id'))->count(),
        ];

        return compact('assignments', 'sessions', 'stats');
    }

    private function respond(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            $data = $this->indexData();

            return response()->json([
                'message' => $message,
                'html' => [
                    '#assignments-summary' => view('admin.assignments.partials.summary', $data)->render(),
                    '#assignments-list' => view('admin.assignments.partials.list', $data)->render(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
