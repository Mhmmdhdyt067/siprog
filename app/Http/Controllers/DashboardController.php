<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === UserRole::MAHASISWA) {
            $activeSession = PracticumSession::where('classroom_id', $user->classroom_id)
                ->where('status', 'active')
                ->first();

            $pendingFeedbackSessions = PracticumSession::where('classroom_id', $user->classroom_id)
                ->where('status', 'finished')
                ->whereHas('attendances', fn ($q) => $q->where('user_id', $user->id)->where('status', 'hadir'))
                ->whereDoesntHave('feedbacks', fn ($q) => $q->where('user_id', $user->id))
                ->get();

            $announcements = Announcement::where('is_published', true)
                ->where(fn ($q) => $q->whereNull('classroom_id')->orWhere('classroom_id', $user->classroom_id))
                ->latest()
                ->take(5)
                ->get();

            $graduation = $user->graduation_status;

            return view('dashboard.mahasiswa', compact(
                'user', 'activeSession', 'pendingFeedbackSessions', 'announcements', 'graduation'
            ));
        }

        if ($user->role === UserRole::ASISTEN) {
            $classroomIds = $user->assistedClassrooms()->pluck('classrooms.id');

            $activeSessions = PracticumSession::with('classroom')
                ->whereIn('classroom_id', $classroomIds)
                ->where('status', 'active')
                ->get();

            $recentSubmissions = AssignmentSubmission::with(['user', 'assignment.session'])
                ->whereHas('assignment.session', fn ($q) => $q->whereIn('classroom_id', $classroomIds))
                ->latest('submitted_at')
                ->take(8)
                ->get();

            $charts = $this->assistantCharts($classroomIds);

            return view('dashboard.asisten', compact('user', 'activeSessions', 'recentSubmissions', 'charts'));
        }

        $stats = [
            'total_classes' => Classroom::count(),
            'total_students' => User::where('role', UserRole::MAHASISWA)->count(),
            'total_assistants' => User::where('role', UserRole::ASISTEN)->count(),
            'active_sessions' => PracticumSession::where('status', 'active')->count(),
        ];
        $announcements = Announcement::latest()->take(5)->get();
        $charts = $this->adminCharts();

        return view('dashboard.admin', compact('user', 'stats', 'announcements', 'charts'));
    }

    private function adminCharts(): array
    {
        $months = collect(range(5, 0))
            ->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        $sessions = PracticumSession::where('session_date', '>=', $months->first()->toDateString())
            ->get(['session_date', 'status']);

        $sessionTrend = $months->map(function ($month) use ($sessions) {
            return $sessions->filter(
                fn ($session) => $session->session_date?->format('Y-m') === $month->format('Y-m')
            )->count();
        });

        $finishedTrend = $months->map(function ($month) use ($sessions) {
            return $sessions->filter(
                fn ($session) => $session->status === 'finished'
                    && $session->session_date?->format('Y-m') === $month->format('Y-m')
            )->count();
        });

        $attendance = Attendance::select('status')->get()->countBy('status');

        $classScores = Grade::with('student.classroom')
            ->get()
            ->filter(fn ($grade) => $grade->student?->classroom)
            ->groupBy(fn ($grade) => $grade->student->classroom->name)
            ->map(fn (Collection $grades) => round((float) $grades->avg('total_score'), 2))
            ->sortKeys();

        $classes = Classroom::withCount('students')->orderBy('name')->get();

        return [
            'labels' => $months->map(fn ($month) => $month->locale('id')->translatedFormat('M'))->all(),
            'sessionTrend' => $sessionTrend->all(),
            'finishedTrend' => $finishedTrend->all(),
            'attendance' => [
                'hadir' => (int) $attendance->get('hadir', 0),
                'izin' => (int) $attendance->get('izin', 0),
                'sakit' => (int) $attendance->get('sakit', 0),
                'alpha' => (int) $attendance->get('alpha', 0),
            ],
            'classScores' => [
                'labels' => $classScores->keys()->all(),
                'data' => $classScores->values()->all(),
            ],
            'classStudents' => [
                'labels' => $classes->pluck('name')->all(),
                'data' => $classes->pluck('students_count')->all(),
            ],
        ];
    }

    private function assistantCharts(Collection $classroomIds): array
    {
        $sessions = PracticumSession::with('classroom')
            ->whereIn('classroom_id', $classroomIds)
            ->latest('session_date')
            ->latest('id')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $studentCounts = User::where('role', UserRole::MAHASISWA)
            ->whereIn('classroom_id', $classroomIds)
            ->get(['classroom_id'])
            ->countBy('classroom_id');

        $present = Attendance::whereIn('practicum_session_id', $sessions->pluck('id'))
            ->where('status', 'hadir')
            ->get(['practicum_session_id'])
            ->countBy('practicum_session_id');

        $sessionLabels = $sessions->map(
            fn ($session) => 'P' . $session->meeting_number . ' · ' . ($session->classroom?->name ?? '—')
        )->all();

        $presentData = $sessions->map(fn ($session) => (int) $present->get($session->id, 0))->all();

        $absentData = $sessions->map(function ($session) use ($present, $studentCounts) {
            $total = (int) $studentCounts->get($session->classroom_id, 0);
            return max(0, $total - (int) $present->get($session->id, 0));
        })->all();

        $sessionIds = $sessions->pluck('id');

        $grades = Grade::whereIn('practicum_session_id', $sessionIds)->get(['total_score']);
        $buckets = [
            '< 50' => $grades->filter(fn ($g) => (float) $g->total_score < 50)->count(),
            '50–69' => $grades->filter(fn ($g) => (float) $g->total_score >= 50 && (float) $g->total_score < 70)->count(),
            '70–84' => $grades->filter(fn ($g) => (float) $g->total_score >= 70 && (float) $g->total_score < 85)->count(),
            '85–100' => $grades->filter(fn ($g) => (float) $g->total_score >= 85)->count(),
        ];

        $submissions = AssignmentSubmission::whereHas(
            'assignment',
            fn ($q) => $q->whereIn('practicum_session_id', $sessionIds)
        )->get(['assignment_id']);

        $submissionsByAssignment = $submissions->countBy('assignment_id');

        $assignmentIdsBySession = \App\Models\Assignment::whereIn('practicum_session_id', $sessionIds)
            ->get(['id', 'practicum_session_id'])
            ->groupBy('practicum_session_id');

        $submissionData = $sessions->map(function ($session) use ($assignmentIdsBySession, $submissionsByAssignment) {
            $ids = $assignmentIdsBySession->get($session->id, collect())->pluck('id');
            return $ids->sum(fn ($id) => (int) $submissionsByAssignment->get($id, 0));
        })->all();

        return [
            'sessionLabels' => $sessionLabels,
            'present' => $presentData,
            'absent' => $absentData,
            'scoreBuckets' => [
                'labels' => array_keys($buckets),
                'data' => array_values($buckets),
            ],
            'submissions' => $submissionData,
        ];
    }
}
