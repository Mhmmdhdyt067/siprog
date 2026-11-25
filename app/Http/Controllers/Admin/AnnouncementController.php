<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Classroom;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements.index', $this->indexData());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:info,jadwal,tugas'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
        ]);

        Announcement::create([
            ...$validated,
            'created_by' => auth()->id(),
            'is_published' => true,
        ]);

        return $this->respond($request, 'Pemberitahuan berhasil dipublikasikan.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'type' => ['required', 'in:info,jadwal,tugas'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
        ]);

        $announcement->update($validated);

        return $this->respond($request, 'Pemberitahuan berhasil diperbarui.');
    }

    public function destroy(Request $request, Announcement $announcement)
    {
        $announcement->delete();

        return $this->respond($request, 'Pemberitahuan berhasil dihapus.');
    }

    private function indexData(): array
    {
        $announcements = Announcement::with('classroom')->latest()->paginate(12);

        $all = Announcement::select('type')->get()->countBy('type');

        $stats = [
            'total' => Announcement::count(),
            'info' => $all->get('info', 0),
            'jadwal' => $all->get('jadwal', 0),
            'tugas' => $all->get('tugas', 0),
        ];

        $classrooms = Classroom::orderBy('name')->get();

        return compact('announcements', 'classrooms', 'stats');
    }

    private function respond(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            $data = $this->indexData();

            return response()->json([
                'message' => $message,
                'html' => [
                    '#announcements-summary' => view('admin.announcements.partials.summary', $data)->render(),
                    '#announcements-list' => view('admin.announcements.partials.list', $data)->render(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
