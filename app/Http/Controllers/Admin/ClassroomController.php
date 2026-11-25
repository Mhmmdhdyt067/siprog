<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassroomController extends Controller
{
    public function index()
    {
        return view('admin.master.classrooms', $this->indexData());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:classrooms,code'],
            'name' => ['required', 'string', 'max:100'],
            'course_name' => ['required', 'string', 'max:150'],
            'schedule_day' => ['required', 'string'],
            'schedule_time' => ['required', 'string'],
            'room' => ['required', 'string'],
        ]);

        Classroom::create($validated);

        return $this->respond($request, 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:classrooms,code,'.$classroom->id],
            'name' => ['required', 'string', 'max:100'],
            'course_name' => ['required', 'string', 'max:150'],
            'schedule_day' => ['required', 'string'],
            'schedule_time' => ['required', 'string'],
            'room' => ['required', 'string'],
        ]);

        $classroom->update($validated);

        return $this->respond($request, 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Request $request, Classroom $classroom)
    {
        $files = [];
        $classroom->sessions()->with('assignments.submissions')->get()->each(function ($session) use (&$files) {
            foreach ($session->assignments as $assignment) {
                $files[] = $assignment->file_path;
                $files = array_merge($files, $assignment->submissions->pluck('file_path')->all());
            }
        });

        $classroom->delete();
        Storage::disk('local')->delete(array_filter($files));

        return $this->respond($request, 'Data kelas berhasil dihapus.');
    }

    private function indexData(): array
    {
        $classrooms = Classroom::withCount(['students', 'assistants', 'sessions'])
            ->orderBy('name')
            ->get();

        $stats = [
            'classrooms' => $classrooms->count(),
            'students' => $classrooms->sum('students_count'),
            'assistants' => $classrooms->sum('assistants_count'),
            'sessions' => $classrooms->sum('sessions_count'),
        ];

        return compact('classrooms', 'stats');
    }

    private function respond(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            $data = $this->indexData();

            return response()->json([
                'message' => $message,
                'html' => [
                    '#classrooms-summary' => view('admin.master.partials.classroom-summary', $data)->render(),
                    '#classrooms-list' => view('admin.master.partials.classroom-list', $data)->render(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
