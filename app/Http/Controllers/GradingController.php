<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AssignmentSubmission;
use App\Models\Grade;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GradingController extends Controller
{
    public function index(PracticumSession $session)
    {
        $this->ensureAccess($session);

        $students = $this->students($session);
        $stats = $this->stats($students);

        return view('grading.index', compact('session', 'students', 'stats'));
    }

    public function store(Request $request, PracticumSession $session)
    {
        $this->ensureAccess($session);

        $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.pretest_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'grades.*.assignment_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'grades.*.extra_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'grades.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->ensureStudentsInClass($session, array_keys($request->grades));

        DB::transaction(function () use ($request, $session) {
            foreach ($request->grades as $studentId => $data) {
                $this->saveGrade($session, (int) $studentId, $data);
            }
        });

        $message = "Nilai Pertemuan {$session->meeting_number} berhasil disimpan!";

        if ($request->wantsJson()) {
            $students = $this->students($session);

            return response()->json([
                'message' => $message,
                'html' => [
                    '#grading-summary' => view('grading.partials.summary', [
                        'session' => $session,
                        'stats' => $this->stats($students),
                    ])->render(),
                    '#grading-list' => view('grading.partials.list', compact('session', 'students'))->render(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    public function storeStudent(Request $request, PracticumSession $session, User $student)
    {
        $this->ensureAccess($session);

        abort_unless($student->role === UserRole::MAHASISWA && $student->classroom_id === $session->classroom_id, 404);

        $validated = $request->validate([
            'pretest_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'assignment_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'extra_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->saveGrade($session, $student->id, $validated);

        $message = "Nilai {$student->name} berhasil disimpan.";

        if ($request->wantsJson()) {
            $students = $this->students($session);
            $mhs = $students->firstWhere('id', $student->id);

            return response()->json([
                'message' => $message,
                'html' => [
                    '#grading-summary' => view('grading.partials.summary', [
                        'session' => $session,
                        'stats' => $this->stats($students),
                    ])->render(),
                    '#grading-row-'.$student->id => view('grading.partials.row', [
                        'session' => $session,
                        'mhs' => $mhs,
                    ])->render(),
                ],
                'total' => $mhs?->grades->first()?->total_score,
            ]);
        }

        return back()->with('success', $message);
    }

    public function downloadSubmission(Request $request, AssignmentSubmission $submission)
    {
        $this->ensureAccess($submission->assignment->session);

        if (! Storage::disk('local')->exists($submission->file_path)) {
            return back()->with('error', 'File PDF tidak ditemukan.');
        }

        if ($request->boolean('download')) {
            return Storage::disk('local')->download(
                $submission->file_path,
                $submission->original_filename ?: basename($submission->file_path)
            );
        }

        return Storage::disk('local')->response($submission->file_path);
    }

    private function ensureAccess(PracticumSession $session): void
    {
        $user = auth()->user();

        if ($user->role !== UserRole::ASISTEN) {
            return;
        }

        abort_unless(
            $user->assistedClassrooms()->where('classrooms.id', $session->classroom_id)->exists(),
            403,
            'Anda hanya dapat menilai sesi pada kelas yang diampu.'
        );
    }

    private function ensureStudentsInClass(PracticumSession $session, array $studentIds): void
    {
        $valid = User::where('role', UserRole::MAHASISWA)
            ->where('classroom_id', $session->classroom_id)
            ->whereIn('id', $studentIds)
            ->count();

        abort_unless(
            $valid === count($studentIds),
            422,
            'Ada mahasiswa yang bukan anggota kelas sesi ini.'
        );
    }

    private function saveGrade(PracticumSession $session, int $studentId, array $data): void
    {
        $computedTotal = min(
            100,
            round(((float) $data['pretest_score'] * 0.4) + ((float) $data['assignment_score'] * 0.6) + (float) $data['extra_score'], 2)
        );

        Grade::updateOrCreate(
            [
                'practicum_session_id' => $session->id,
                'user_id' => $studentId,
            ],
            [
                'graded_by' => auth()->id(),
                'pretest_score' => $data['pretest_score'],
                'assignment_score' => $data['assignment_score'],
                'extra_score' => $data['extra_score'],
                'total_score' => $computedTotal,
                'notes' => $data['notes'] ?? null,
            ]
        );
    }

    private function students(PracticumSession $session)
    {
        return User::where('role', UserRole::MAHASISWA)
            ->where('classroom_id', $session->classroom_id)
            ->with([
                'attendances' => fn ($q) => $q->where('practicum_session_id', $session->id),
                'grades' => fn ($q) => $q->where('practicum_session_id', $session->id),
                'submissions' => fn ($q) => $q->whereHas('assignment', fn ($a) => $a->where('practicum_session_id', $session->id)),
            ])
            ->orderBy('nim_nip')
            ->get();
    }

    private function stats($students): array
    {
        $graded = $students->filter(fn ($mhs) => $mhs->grades->isNotEmpty());

        return [
            'students' => $students->count(),
            'graded' => $graded->count(),
            'pending' => $students->count() - $graded->count(),
            'attendance' => $students->filter(fn ($mhs) => $mhs->attendances->first()?->status === 'hadir')->count(),
            'average' => round($graded->avg(fn ($mhs) => (float) $mhs->grades->first()->total_score) ?? 0, 2),
        ];
    }
}
