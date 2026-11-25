<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Exports\SessionGradesExport;
use App\Exports\StudentOverallGradesExport;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('students')->orderBy('name')->get();

        $sessions = PracticumSession::with([
            'classroom' => fn ($query) => $query->withCount('students'),
        ])
            ->withCount([
                'grades',
                'attendances as present_count' => fn ($query) => $query->where('status', 'hadir'),
            ])
            ->orderBy('classroom_id')
            ->orderBy('meeting_number')
            ->get();

        $stats = [
            'classrooms' => $classrooms->count(),
            'sessions' => $sessions->count(),
            'students' => User::where('role', UserRole::MAHASISWA)->count(),
            'grades' => $sessions->sum('grades_count'),
        ];

        return view('admin.master.grades', compact('classrooms', 'sessions', 'stats'));
    }

    public function exportBySession(PracticumSession $session)
    {
        $filename = "Nilai_{$session->classroom->name}_Pertemuan_{$session->meeting_number}.xlsx";
        return Excel::download(new SessionGradesExport($session), $filename);
    }

    public function exportOverall(Request $request)
    {
        $classroomId = $request->integer('classroom_id') ?: null;
        $filename = "Rekap_Nilai_Keseluruhan_SIPROG.xlsx";
        return Excel::download(new StudentOverallGradesExport($classroomId), $filename);
    }
}
