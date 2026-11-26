<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PracticumSession;

class NilaiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $sessions = PracticumSession::where('classroom_id', $user->classroom_id)
            ->with([
                'grades' => fn ($query) => $query->where('user_id', $user->id),
                'attendances' => fn ($query) => $query->where('user_id', $user->id),
                'assignment' => fn ($query) => $query->with([
                    'submissions' => fn ($submissions) => $submissions->where('user_id', $user->id),
                ]),
            ])
            ->orderBy('meeting_number')
            ->get();

        $graduation = $user->graduation_status;

        $gradedCount = $sessions->filter(fn (PracticumSession $session) => $session->grades->isNotEmpty())->count();
        $submittedCount = $sessions
            ->filter(fn (PracticumSession $session) => $session->assignment?->submissions->isNotEmpty())
            ->count();
        $assignmentCount = $sessions->filter(fn (PracticumSession $session) => $session->assignment !== null)->count();

        return view('mahasiswa.nilai', compact(
            'sessions',
            'graduation',
            'gradedCount',
            'submittedCount',
            'assignmentCount',
        ));
    }
}
