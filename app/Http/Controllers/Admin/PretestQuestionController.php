<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PracticumSession;
use App\Models\PretestQuestion;
use Illuminate\Http\Request;

class PretestQuestionController extends Controller
{
    public function index(PracticumSession $session)
    {
        $this->authorizeManagedSession($session);

        return view('admin.pretest.index', $this->indexData($session));
    }

    public function overview()
    {
        $sessions = PracticumSession::with('classroom')
            ->withCount('pretestQuestions')
            ->whereIn('classroom_id', $this->managedClassroomIds())
            ->latest('session_date')
            ->latest('id')
            ->paginate(15);

        return view('admin.pretest.overview', compact('sessions'));
    }

    public function store(Request $request, PracticumSession $session)
    {
        $this->authorizeManagedSession($session);

        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'code_snippet' => ['nullable', 'string'],
            'code_language' => ['required', 'in:php,cpp,python,java,javascript,html,sql'],
            'order' => ['required', 'integer', 'min:1'],
        ]);

        $session->pretestQuestions()->create($validated);

        return $this->respond($request, 'Soal Pre-Test berhasil ditambahkan.', $session);
    }

    public function update(Request $request, PretestQuestion $question)
    {
        $this->authorizeManagedSession($question->session);

        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'code_snippet' => ['nullable', 'string'],
            'code_language' => ['required', 'in:php,cpp,python,java,javascript,html,sql'],
            'order' => ['required', 'integer', 'min:1'],
        ]);

        $question->update($validated);

        return $this->respond($request, 'Soal Pre-Test berhasil diperbarui.', $question->session);
    }

    public function destroy(Request $request, PretestQuestion $question)
    {
        $this->authorizeManagedSession($question->session);

        $session = $question->session;
        $question->delete();

        return $this->respond($request, 'Soal Pre-Test berhasil dihapus.', $session);
    }

    private function indexData(PracticumSession $session): array
    {
        $questions = $session->pretestQuestions()->get();

        $stats = [
            'total' => $questions->count(),
            'with_code' => $questions->whereNotNull('code_snippet')->count(),
            'languages' => $questions->pluck('code_language')->unique()->count(),
        ];

        return compact('session', 'questions', 'stats');
    }

    private function respond(Request $request, string $message, PracticumSession $session)
    {
        if ($request->wantsJson()) {
            $data = $this->indexData($session);

            return response()->json([
                'message' => $message,
                'html' => [
                    '#pretest-summary' => view('admin.pretest.partials.summary', $data)->render(),
                    '#pretest-list' => view('admin.pretest.partials.list', $data)->render(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
