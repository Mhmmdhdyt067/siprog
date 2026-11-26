<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class PretestMonitorController extends Controller
{
    public function index()
    {
        return view('admin.pretest-monitor.index', $this->indexData());
    }

    public function data()
    {
        return response()->json([
            'message' => 'ok',
            'html' => $this->html($this->indexData()),
        ]);
    }

    public function warn(Request $request, Attendance $attendance)
    {
        $this->authorizeAttendance($attendance);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:255'],
        ]);

        $attendance->update([
            'pretest_warning' => $validated['message'],
            'pretest_warning_at' => now(),
        ]);

        return response()->json([
            'message' => 'Teguran terkirim.',
            'html' => $this->html($this->indexData()),
        ]);
    }

    public function unlock(Attendance $attendance)
    {
        $this->authorizeAttendance($attendance);

        abort_if($attendance->pretest_finished_at, 422, 'Pre-test sudah selesai.');

        $attendance->update([
            'pretest_locked_at' => null,
            'pretest_violations' => 0,
            'pretest_last_violation_at' => null,
        ]);

        return response()->json([
            'message' => 'Akses pre-test dibuka.',
            'html' => $this->html($this->indexData()),
        ]);
    }

    private function authorizeAttendance(Attendance $attendance): void
    {
        $attendance->loadMissing('session');
        $this->authorizeManagedSession($attendance->session);
    }

    private function indexData(): array
    {
        $attempts = Attendance::with(['user', 'session.classroom'])
            ->where('status', 'hadir')
            ->whereNotNull('pretest_started_at')
            ->whereNull('pretest_finished_at')
            ->whereHas('session', fn ($q) => $q->whereIn('classroom_id', $this->managedClassroomIds()))
            ->orderByDesc('pretest_violations')
            ->orderBy('pretest_deadline')
            ->get();

        $stats = [
            'active' => $attempts->count(),
            'locked' => $attempts->whereNotNull('pretest_locked_at')->count(),
            'flagged' => $attempts->where('pretest_violations', '>', 0)->count(),
        ];

        return compact('attempts', 'stats');
    }

    private function html(array $data): array
    {
        return [
            '#monitor-summary' => view('admin.pretest-monitor.partials.summary', $data)->render(),
            '#monitor-list' => view('admin.pretest-monitor.partials.list', $data)->render(),
        ];
    }
}
