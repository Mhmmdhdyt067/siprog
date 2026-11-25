<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\PracticumSession;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $activeSessions = PracticumSession::where('classroom_id', $user->classroom_id)
            ->where('status', 'active')
            ->get();

        $history = $user->attendances()
            ->with('session')
            ->latest('scanned_at')
            ->get();

        return view('mahasiswa.presensi', compact('activeSessions', 'history'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => ['required', 'string'],
        ]);

        $token = trim($request->qr_data);
        $user = auth()->user();

        // Hanya token QR terbaru yang valid. Token lama langsung mati saat QR
        // dirotasi, bukan menunggu masa berlakunya habis.
        $session = PracticumSession::where('qr_token', $token)->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau sudah diganti. Pindai QR Code terbaru di layar.',
            ], 404);
        }

        if ($session->classroom_id !== $user->classroom_id) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code ini bukan untuk kelas Anda.',
            ], 403);
        }

        if ($session->status !== 'active' || ($session->qr_expires_at && now()->greaterThan($session->qr_expires_at))) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi presensi sudah ditutup atau QR Code kedaluwarsa.',
            ], 422);
        }

        // createOrFirst, not firstOrCreate: two tabs scanning at once would race
        // the unique index and the loser threw a 500.
        $attendance = Attendance::createOrFirst(
            [
                'practicum_session_id' => $session->id,
                'user_id' => $user->id,
            ],
            [
                'status' => 'hadir',
                'scanned_at' => now(),
            ]
        );

        if ($attendance->status !== 'hadir') {
            $attendance->update(['status' => 'hadir', 'scanned_at' => now()]);
        }

        $hasPretest = $session->pretestQuestions()->exists();

        return response()->json([
            'success' => true,
            'message' => "Presensi Pertemuan {$session->meeting_number} berhasil dicatat!",
            'session_title' => $session->title,
            'has_pretest' => $hasPretest,
            'pretest_url' => route('mahasiswa.pretest.show', $session),
            'dashboard_url' => route('dashboard'),
        ]);
    }
}
