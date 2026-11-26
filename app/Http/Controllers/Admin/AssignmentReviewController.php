<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignmentReviewController extends Controller
{
    public function index()
    {
        $managedIds = $this->managedClassroomIds();

        $assignments = Assignment::with('session.classroom')
            ->whereHas('session', fn ($q) => $q->whereIn('classroom_id', $managedIds))
            ->withCount('submissions')
            ->latest()
            ->paginate(12);

        $classroomIds = $assignments->getCollection()
            ->pluck('session.classroom_id')
            ->filter()
            ->unique()
            ->values();

        $studentCounts = User::query()
            ->where('role', UserRole::MAHASISWA)
            ->whereIn('classroom_id', $classroomIds)
            ->select('classroom_id', DB::raw('count(*) as total'))
            ->groupBy('classroom_id')
            ->pluck('total', 'classroom_id');

        $sessionIds = $assignments->getCollection()
            ->pluck('practicum_session_id')
            ->unique()
            ->values();

        $gradedCounts = Grade::query()
            ->whereNotNull('assignment_score')
            ->whereIn('practicum_session_id', $sessionIds)
            ->select('practicum_session_id', DB::raw('count(*) as total'))
            ->groupBy('practicum_session_id')
            ->pluck('total', 'practicum_session_id');

        $base = Assignment::query()
            ->whereHas('session', fn ($q) => $q->whereIn('classroom_id', $managedIds));

        $stats = [
            'assignments' => (clone $base)->count(),
            'with_submissions' => (clone $base)->has('submissions')->count(),
            'submissions' => AssignmentSubmission::whereIn('assignment_id', (clone $base)->select('id'))->count(),
            'graded' => Grade::whereNotNull('assignment_score')
                ->whereIn('practicum_session_id', (clone $base)->select('practicum_session_id'))
                ->count(),
        ];

        return view('admin.submissions.index', compact(
            'assignments',
            'studentCounts',
            'gradedCounts',
            'stats',
        ));
    }

    public function show(Assignment $assignment)
    {
        $this->authorizeManagedSession($assignment->session);

        $assignment->load('session.classroom');

        $students = $this->students($assignment);
        $stats = $this->stats($students);

        return view('admin.submissions.show', compact('assignment', 'students', 'stats'));
    }

    public function grade(Request $request, Assignment $assignment, User $student): RedirectResponse|JsonResponse
    {
        $this->authorizeManagedSession($assignment->session);

        abort_unless(
            $student->role === UserRole::MAHASISWA
            && $student->classroom_id === $assignment->session->classroom_id,
            404
        );

        $hasSubmission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('user_id', $student->id)
            ->exists();

        if (! $hasSubmission) {
            throw ValidationException::withMessages([
                'assignment_score' => 'Mahasiswa belum mengumpulkan tugas ini.',
            ]);
        }

        $validated = $request->validate([
            'assignment_score' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'assignment_score.required' => 'Nilai tugas wajib diisi.',
            'assignment_score.numeric' => 'Nilai tugas harus berupa angka.',
            'assignment_score.min' => 'Nilai tugas minimal 0.',
            'assignment_score.max' => 'Nilai tugas maksimal 100.',
        ]);

        $session = $assignment->session;
        $grade = Grade::firstOrNew([
            'practicum_session_id' => $session->id,
            'user_id' => $student->id,
        ]);

        $pretest = (float) ($grade->pretest_score ?? 0);
        $extra = (float) ($grade->extra_score ?? 0);
        $score = (float) $validated['assignment_score'];

        $grade->fill([
            'graded_by' => auth()->id(),
            'pretest_score' => $pretest,
            'assignment_score' => $score,
            'extra_score' => $extra,
            'total_score' => min(100, round(($pretest * 0.4) + ($score * 0.6) + $extra, 2)),
        ])->save();

        $message = "Nilai tugas {$student->name} berhasil disimpan.";

        if ($request->wantsJson()) {
            $assignment->load('session.classroom');
            $students = $this->students($assignment);
            $mhs = $students->firstWhere('id', $student->id);

            return response()->json([
                'message' => $message,
                'html' => [
                    '#submission-summary' => view('admin.submissions.partials.summary', [
                        'assignment' => $assignment,
                        'stats' => $this->stats($students),
                    ])->render(),
                    '#submission-row-'.$student->id => view('admin.submissions.partials.student-row', [
                        'assignment' => $assignment,
                        'mhs' => $mhs,
                    ])->render(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    private function students(Assignment $assignment)
    {
        return User::where('role', UserRole::MAHASISWA)
            ->where('classroom_id', $assignment->session->classroom_id)
            ->with([
                'submissions' => fn ($query) => $query->where('assignment_id', $assignment->id),
                'grades' => fn ($query) => $query->where('practicum_session_id', $assignment->practicum_session_id),
            ])
            ->orderBy('nim_nip')
            ->get();
    }

    private function stats($students): array
    {
        $submitted = $students->filter(fn (User $mhs) => $mhs->submissions->isNotEmpty());
        $scores = $students
            ->map(fn (User $mhs) => $mhs->grades->first()?->assignment_score)
            ->filter(fn ($score) => $score !== null);

        return [
            'total' => $students->count(),
            'submitted' => $submitted->count(),
            'not_submitted' => $students->count() - $submitted->count(),
            'graded' => $scores->count(),
            'average' => $scores->isNotEmpty() ? round((float) $scores->avg(), 2) : null,
            'percentage' => $students->count() > 0
                ? round(($submitted->count() / $students->count()) * 100, 1)
                : 0,
        ];
    }
}
