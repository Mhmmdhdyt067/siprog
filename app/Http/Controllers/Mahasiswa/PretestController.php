<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PracticumSession;
use Illuminate\Support\Facades\DB;

class PretestController extends Controller
{
    const PRETEST_DURATION_MINUTES = 15;

    // Halaman daftar Pre-Test (untuk akses manual lewat menu Pre-Test)
    public function index()
    {
        $user = auth()->user();

        $sessions = PracticumSession::where('classroom_id', $user->classroom_id)
            ->whereHas('pretestQuestions')
            ->with(['attendances' => fn ($q) => $q->where('user_id', $user->id)])
            ->latest('session_date')
            ->get();

        return view('mahasiswa.pretest-index', compact('sessions'));
    }

    // Halaman soal Pre-Test (tanpa kolom jawaban, durasi 15 menit)
    public function show(PracticumSession $session)
    {
        $user = auth()->user();

        abort_if($session->classroom_id !== $user->classroom_id, 403);

        $attendance = Attendance::where('practicum_session_id', $session->id)
            ->where('user_id', $user->id)
            ->where('status', 'hadir')
            ->first();

        if (! $attendance) {
            return redirect()
                ->route('mahasiswa.presensi.index')
                ->with('error', 'Anda harus melakukan scan presensi terlebih dahulu sebelum mengakses Pre-Test.');
        }

        // Jika sudah pernah diselesaikan sebelumnya -> tertutup permanen
        if ($attendance->pretest_finished_at) {
            return view('mahasiswa.pretest-closed', compact('session'));
        }

        if ($attendance->pretest_locked_at) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Akses pre-test dikunci karena pelanggaran. Hubungi asisten untuk membuka kunci.');
        }

        // Mulai timer di database saat pertama kali mahasiswa membuka soal
        if (! $attendance->pretest_started_at) {
            $attendance->update([
                'pretest_started_at' => now(),
                'pretest_deadline' => now()->addMinutes(self::PRETEST_DURATION_MINUTES),
            ]);
        } elseif (! $attendance->pretest_deadline) {
            // Data lama: deadline belum pernah tersimpan
            $attendance->update([
                'pretest_deadline' => $attendance->pretest_started_at->copy()->addMinutes(self::PRETEST_DURATION_MINUTES),
            ]);
        }

        $attendance->refresh();
        $remainingSeconds = $attendance->pretestRemainingSeconds();

        // Jika waktu sudah habis (termasuk karena penalti) -> tutup permanen
        if ($remainingSeconds <= 0) {
            $attendance->update(['pretest_finished_at' => $attendance->pretest_deadline]);

            return view('mahasiswa.pretest-closed', compact('session'));
        }

        $questions = $session->pretestQuestions()->get();

        return view('mahasiswa.pretest-show', compact('session', 'questions', 'remainingSeconds'));
    }

    // Dipanggil saat tombol "Selesai" diklik atau timer 15 menit menyentuh 00:00
    public function finish(PracticumSession $session)
    {
        abort_if($session->classroom_id !== auth()->user()->classroom_id, 403);

        $attendance = Attendance::where('practicum_session_id', $session->id)
            ->where('user_id', auth()->id())
            ->where('status', 'hadir')
            ->firstOrFail();

        if ($attendance->pretest_locked_at) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Akses pre-test dikunci karena pelanggaran. Hubungi asisten untuk membuka kunci.');
        }

        if (! $attendance->pretest_finished_at) {
            $finishedAt = $attendance->pretest_deadline && now()->greaterThanOrEqualTo($attendance->pretest_deadline)
                ? $attendance->pretest_deadline
                : now();

            $attendance->update(['pretest_finished_at' => $finishedAt]);
        }

        return view('mahasiswa.pretest-closed', compact('session'));
    }

    public function state(PracticumSession $session)
    {
        $attendance = $this->attendanceFor($session);
        $this->finalizeIfExpired($attendance);

        return response()->json($this->statePayload($attendance->fresh()));
    }

    public function violation(PracticumSession $session)
    {
        $attendance = $this->attendanceFor($session);
        $this->finalizeIfExpired($attendance);
        $attendance->refresh();

        if ($attendance->pretest_finished_at || $attendance->pretest_locked_at) {
            return response()->json($this->statePayload($attendance));
        }

        [$fresh, $ignored] = DB::transaction(function () use ($attendance) {
            $fresh = Attendance::whereKey($attendance->id)->lockForUpdate()->firstOrFail();
            $now = now();

            if ($fresh->pretest_last_violation_at && $fresh->pretest_last_violation_at->gt($now->copy()->subSeconds(5))) {
                return [$fresh, true];
            }

            $deadline = $fresh->pretest_deadline
                ?? $fresh->pretest_started_at?->copy()->addMinutes(self::PRETEST_DURATION_MINUTES)
                ?? $now->copy()->addMinutes(self::PRETEST_DURATION_MINUTES);

            $violations = $fresh->pretest_violations + 1;
            $newDeadline = $deadline->copy()->subMinutes(5);

            $data = [
                'pretest_violations' => $violations,
                'pretest_last_violation_at' => $now,
                'pretest_deadline' => $newDeadline,
            ];

            if ($violations >= 2) {
                $data['pretest_locked_at'] = $now;
            }

            if ($newDeadline->lessThanOrEqualTo($now)) {
                $data['pretest_finished_at'] = $newDeadline;
            }

            $fresh->update($data);

            return [$fresh, false];
        });

        $fresh = $fresh->fresh();

        if ($fresh->pretest_locked_at) {
            session()->flash('error', 'Akses pre-test dikunci karena pelanggaran. Hubungi asisten untuk membuka kunci.');
        } elseif ($fresh->pretest_finished_at) {
            session()->flash('error', 'Waktu pre-test habis.');
        }

        return response()->json($this->statePayload(
            $fresh,
            $ignored,
            $ignored ? null : 'Pelanggaran tercatat. Waktu pre-test dikurangi 5 menit.'
        ));
    }

    private function attendanceFor(PracticumSession $session): Attendance
    {
        abort_if($session->classroom_id !== auth()->user()->classroom_id, 403);

        return Attendance::where('practicum_session_id', $session->id)
            ->where('user_id', auth()->id())
            ->where('status', 'hadir')
            ->firstOrFail();
    }

    private function finalizeIfExpired(Attendance $attendance): void
    {
        if (! $attendance->pretest_finished_at
            && $attendance->pretest_deadline
            && now()->greaterThanOrEqualTo($attendance->pretest_deadline)) {
            $attendance->update(['pretest_finished_at' => $attendance->pretest_deadline]);
        }
    }

    private function statePayload(Attendance $attendance, bool $ignored = false, ?string $message = null): array
    {
        return [
            'violations' => (int) $attendance->pretest_violations,
            'locked' => $attendance->pretest_locked_at !== null,
            'finished' => $attendance->pretest_finished_at !== null,
            'remaining_seconds' => $attendance->pretestRemainingSeconds(),
            'warning' => $attendance->pretest_warning ? [
                'message' => $attendance->pretest_warning,
                'at' => $attendance->pretest_warning_at?->toIso8601String(),
            ] : null,
            'ignored' => $ignored,
            'message' => $message,
        ];
    }
}
