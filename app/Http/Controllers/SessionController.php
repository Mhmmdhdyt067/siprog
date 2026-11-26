<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\User;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function index()
    {
        return view('sessions.index', $this->indexData(auth()->user()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'meeting_number' => [
                'required', 'integer', 'min:1', 'max:30',
                Rule::unique('practicum_sessions', 'meeting_number')
                    ->where('classroom_id', $request->integer('classroom_id')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'session_date' => ['required', 'date'],
        ], [
            'meeting_number.unique' => 'Pertemuan dengan nomor ini sudah ada di kelas tersebut.',
        ]);

        $this->authorizeClassroom((int) $validated['classroom_id']);

        $token = Str::random(40);
        $expiresAt = now()->addHours(3);

        $session = PracticumSession::create([
            ...$validated,
            'created_by' => auth()->id(),
            'qr_token' => $token,
            'qr_expires_at' => $expiresAt,
            'status' => 'active',
        ]);

        $session->qrTokens()->create([
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sesi praktikum dibuat & QR proyektor aktif.',
                'redirect' => route('sessions.qr', $session),
            ], 201);
        }

        return redirect()
            ->route('sessions.qr', $session)
            ->with('success', 'Sesi praktikum berhasil dibuat & QR Code aktif.');
    }

    public function update(Request $request, PracticumSession $session)
    {
        $this->ensureAccess($session);

        $validated = $request->validate([
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'meeting_number' => [
                'required', 'integer', 'min:1', 'max:30',
                Rule::unique('practicum_sessions', 'meeting_number')
                    ->where('classroom_id', $request->integer('classroom_id'))
                    ->ignore($session->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'session_date' => ['required', 'date'],
        ], [
            'meeting_number.unique' => 'Pertemuan dengan nomor ini sudah ada di kelas tersebut.',
        ]);

        $this->authorizeClassroom((int) $validated['classroom_id']);

        if ((int) $validated['classroom_id'] !== $session->classroom_id
            && ($session->attendances()->exists()
                || $session->grades()->exists()
                || $session->assignment()->exists()
                || $session->pretestQuestions()->exists())) {
            throw ValidationException::withMessages([
                'classroom_id' => 'Kelas sesi tidak dapat diubah karena sudah memiliki presensi, nilai, tugas, atau soal pre-test.',
            ]);
        }

        $session->update($validated);

        return $this->respond($request, 'Sesi praktikum berhasil diperbarui.', $session);
    }

    public function destroy(Request $request, PracticumSession $session)
    {
        $this->ensureAccess($session);

        $files = $session->assignments()
            ->with('submissions')
            ->get()
            ->flatMap(fn ($assignment) => collect([$assignment->file_path])
                ->merge($assignment->submissions->pluck('file_path')))
            ->filter()
            ->all();

        $session->delete();
        Storage::disk('local')->delete($files);

        return $this->respond($request, 'Sesi praktikum dan data terkait berhasil dihapus.');
    }

    public function showQr(PracticumSession $session)
    {
        $this->ensureAccess($session);

        if ($session->status !== 'active') {
            return redirect()
                ->route('sessions.index')
                ->with('error', 'Sesi ini sudah selesai. QR presensi tidak dapat ditampilkan lagi.');
        }

        if (! $session->qr_expires_at || $session->qr_expires_at->isPast()) {
            $this->issueQrToken($session);
        }

        $qrSvg = $this->qrSvg($session->qr_token);
        $attendees = $session->attendances()->with('user')->latest('scanned_at')->get();
        $totalStudents = User::where('role', UserRole::MAHASISWA)
            ->where('classroom_id', $session->classroom_id)
            ->count();

        return view('sessions.qr', compact('session', 'qrSvg', 'attendees', 'totalStudents'));
    }

    public function attendees(PracticumSession $session)
    {
        $this->ensureAccess($session);

        $attendees = $session->attendances()->with('user')->latest('scanned_at')->get();
        $totalStudents = User::where('role', UserRole::MAHASISWA)
            ->where('classroom_id', $session->classroom_id)
            ->count();

        return response()->json([
            'present' => $attendees->where('status', 'hadir')->count(),
            'total' => $totalStudents,
            'html' => view('sessions.partials.attendees', compact('attendees'))->render(),
            'updated_at' => now()->format('H:i:s'),
        ]);
    }

    public function refreshQr(PracticumSession $session)
    {
        $this->ensureAccess($session);

        if ($session->status !== 'active') {
            $message = 'Sesi sudah selesai. QR presensi tidak dapat diperbarui.';

            if (request()->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $rotated = $this->issueQrToken($session, request()->boolean('force'));

        $message = $rotated ? 'Token QR Code berhasil diperbarui.' : 'QR Code masih yang terbaru.';

        if (request()->wantsJson()) {
            return response()->json([
                'message' => $message,
                'rotated' => $rotated,
                'qr_svg' => $this->qrSvg($session->qr_token),
                'expires_at' => $session->qr_expires_at?->format('H:i'),
                'next_rotate_in' => $this->secondsUntilRotation($session),
            ]);
        }

        return back()->with('success', $message);
    }

    public function finish(Request $request, PracticumSession $session)
    {
        $this->ensureAccess($session);

        $session->update(['status' => 'finished']);

        $message = 'Sesi praktikum selesai. Mahasiswa kini dapat mengisi feedback.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('sessions.index'),
            ]);
        }

        return back()->with('success', $message);
    }

    private function indexData(User $user): array
    {
        $scope = fn ($query) => $query->when(
            $user->role === UserRole::ASISTEN,
            fn ($q) => $q->whereIn('classroom_id', $user->assistedClassrooms()->pluck('classrooms.id'))
        );

        $sessions = $scope(PracticumSession::query())
            ->with('classroom')
            ->withCount(['attendances', 'grades'])
            ->latest('session_date')
            ->latest('id')
            ->paginate(12);

        $classrooms = $user->role === UserRole::ADMIN
            ? Classroom::orderBy('name')->get()
            : $user->assistedClassrooms()->orderBy('name')->get();

        $base = $scope(PracticumSession::query());

        $stats = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', 'active')->count(),
            'finished' => (clone $base)->where('status', 'finished')->count(),
            'attendance' => Attendance::whereIn('practicum_session_id', (clone $base)->select('id'))->count(),
        ];

        return compact('sessions', 'classrooms', 'stats');
    }

    private function respond(Request $request, string $message, ?PracticumSession $session = null): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            $data = $this->indexData(auth()->user());

            return response()->json([
                'message' => $message,
                'html' => [
                    '#sessions-summary' => view('sessions.partials.summary', $data)->render(),
                    '#sessions-list' => view('sessions.partials.list', $data)->render(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    private function qrSvg(string $token): string
    {
        return Builder::create()
            ->writer(new SvgWriter)
            ->data($token)
            ->size(380)
            ->margin(10)
            ->build()
            ->getString();
    }

    /**
     * Rotasi token QR. Mengembalikan true kalau token benar-benar berganti.
     */
    private function issueQrToken(PracticumSession $session, bool $force = false): bool
    {
        $latest = $session->qrTokens()->latest('id')->first();

        // Dua tab/perangkat yang polling bersamaan tidak boleh saling mengganti
        // token. Rotasi otomatis dibatasi sekali per 25 detik; tab lain memakai
        // token yang sama. Tombol manual mengirim force.
        if (! $force && $latest && $latest->created_at->gt(now()->subSeconds(25))) {
            return false;
        }

        $current = $session->qr_token;
        $token = Str::random(40);
        $expiresAt = now()->addHours(3);

        // Bandingkan token lama secara atomik: kalau tab lain sudah lebih dulu
        // merotasi, update ini tidak mengenai baris apa pun dan kita pakai token
        // milik tab tersebut.
        $query = PracticumSession::whereKey($session->id);

        if ($current === null) {
            $query->whereNull('qr_token');
        } else {
            $query->where('qr_token', $current);
        }

        $updated = $query->update([
            'qr_token' => $token,
            'qr_expires_at' => $expiresAt,
            'updated_at' => now(),
        ]);

        if ($updated === 0) {
            $session->refresh();

            return false;
        }

        DB::transaction(function () use ($session, $token, $expiresAt) {
            // Tabel qr_tokens hanya menyimpan token yang aktif.
            $session->qrTokens()->delete();
            $session->qrTokens()->create([
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);
        });

        $session->refresh();

        return true;
    }

    private function secondsUntilRotation(PracticumSession $session): int
    {
        $latest = $session->qrTokens()->latest('id')->first();

        if (! $latest) {
            return 1;
        }

        return max(1, ($latest->created_at->getTimestamp() + 26) - now()->getTimestamp());
    }

    private function ensureAccess(PracticumSession $session): void
    {
        $user = auth()->user();

        if ($user->role !== UserRole::ASISTEN) {
            return;
        }

        $owns = $user->assistedClassrooms()
            ->where('classrooms.id', $session->classroom_id)
            ->exists();

        abort_unless($owns, 403);
    }

    private function authorizeClassroom(int $classroomId): void
    {
        $user = auth()->user();

        if ($user->role !== UserRole::ASISTEN) {
            return;
        }

        $owns = $user->assistedClassrooms()
            ->where('classrooms.id', $classroomId)
            ->exists();

        abort_unless($owns, 403, 'Anda hanya dapat mengelola kelas yang diampu.');
    }
}
