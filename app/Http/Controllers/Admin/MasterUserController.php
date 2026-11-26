<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MasterUserController extends Controller
{
    public function students(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $students = User::where('role', UserRole::MAHASISWA)
            ->with('classroom')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nim_nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $classrooms = Classroom::orderBy('name')->get();
        $stats = $this->stats(UserRole::MAHASISWA, $classrooms);

        return view('admin.master.students', compact('students', 'classrooms', 'stats', 'search'));
    }

    public function assistants(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $assistants = User::where('role', UserRole::ASISTEN)
            ->with('assistedClassrooms')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nim_nip', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $classrooms = Classroom::orderBy('name')->get();
        $stats = $this->stats(UserRole::ASISTEN, $classrooms);

        return view('admin.master.assistants', compact('assistants', 'classrooms', 'stats', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')],
            'nim_nip' => ['required', 'string', 'unique:users,nim_nip'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:mahasiswa,asisten'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'assisted_classroom_ids' => ['nullable', 'array'],
            'assisted_classroom_ids.*' => ['exists:classrooms,id'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.unique' => 'Nama lengkap sudah dipakai akun lain. Nama menjadi username login, jadi harus unik.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nim_nip' => $validated['nim_nip'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'classroom_id' => $validated['role'] === 'mahasiswa' ? ($validated['classroom_id'] ?? null) : null,
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($validated['role'] === 'asisten' && ! empty($validated['assisted_classroom_ids'])) {
            $user->assistedClassrooms()->sync($validated['assisted_classroom_ids']);
        }

        return $this->respond($request, 'Data pengguna berhasil ditambahkan.', $validated['role']);
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role === UserRole::ADMIN, 403);

        $role = $user->role === UserRole::ASISTEN ? 'asisten' : 'mahasiswa';

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($user->id)],
            'nim_nip' => ['required', 'string', Rule::unique('users', 'nim_nip')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'assisted_classroom_ids' => ['nullable', 'array'],
            'assisted_classroom_ids.*' => ['exists:classrooms,id'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [
            'name.unique' => 'Nama lengkap sudah dipakai akun lain. Nama menjadi username login, jadi harus unik.',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'nim_nip' => $validated['nim_nip'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'classroom_id' => $role === 'mahasiswa' ? ($validated['classroom_id'] ?? null) : null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($role === 'asisten') {
            $user->assistedClassrooms()->sync($validated['assisted_classroom_ids'] ?? []);
        }

        return $this->respond($request, 'Data pengguna berhasil diperbarui.', $role);
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->role === UserRole::ADMIN, 403);

        $role = $user->role === UserRole::ASISTEN ? 'asisten' : 'mahasiswa';
        $files = AssignmentSubmission::where('user_id', $user->id)->pluck('file_path')->all();

        $user->delete();
        Storage::disk('local')->delete(array_filter($files));

        return $this->respond($request, 'Data pengguna berhasil dihapus.', $role);
    }

    private function stats(UserRole $role, $classrooms): array
    {
        return [
            'users' => User::where('role', $role)->count(),
            'classrooms' => $classrooms->count(),
            'without_class' => $role === UserRole::MAHASISWA
                ? User::where('role', $role)->whereNull('classroom_id')->count()
                : User::where('role', $role)->whereDoesntHave('assistedClassrooms')->count(),
            'new_this_month' => User::where('role', $role)
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
        ];
    }

    private function respond(Request $request, string $message, string $role)
    {
        if ($request->wantsJson()) {
            $classrooms = Classroom::orderBy('name')->get();
            $enum = $role === 'asisten' ? UserRole::ASISTEN : UserRole::MAHASISWA;

            if ($role === 'asisten') {
                $assistants = User::where('role', $enum)->with('assistedClassrooms')->latest()->paginate(20);
                $stats = $this->stats($enum, $classrooms);

                return response()->json([
                    'message' => $message,
                    'html' => [
                        '#assistants-summary' => view('admin.master.partials.user-summary', [
                            'stats' => $stats,
                            'role' => 'asisten',
                        ])->render(),
                        '#assistants-list' => view('admin.master.partials.assistant-list', compact('assistants'))->render(),
                    ],
                ]);
            }

            $students = User::where('role', $enum)->with('classroom')->latest()->paginate(20);
            $stats = $this->stats($enum, $classrooms);

            return response()->json([
                'message' => $message,
                'html' => [
                    '#students-summary' => view('admin.master.partials.user-summary', [
                        'stats' => $stats,
                        'role' => 'mahasiswa',
                    ])->render(),
                    '#students-list' => view('admin.master.partials.student-list', compact('students'))->render(),
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
