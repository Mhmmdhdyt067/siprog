<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Feedback;
use App\Models\PracticumSession;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request, PracticumSession $session)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        abort_if($session->classroom_id !== auth()->user()->classroom_id, 403);

        abort_if($session->status !== 'finished', 403, 'Feedback hanya dapat diisi setelah praktikum selesai.');

        $attended = Attendance::where('practicum_session_id', $session->id)
            ->where('user_id', auth()->id())
            ->where('status', 'hadir')
            ->exists();

        abort_unless($attended, 403, 'Feedback hanya dapat diisi oleh mahasiswa yang tercatat hadir.');

        Feedback::updateOrCreate(
            [
                'practicum_session_id' => $session->id,
                'user_id' => auth()->id(),
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        return back()->with('success', 'Terima kasih! Tanggapan praktikum Anda berhasil disimpan.');
    }
}
