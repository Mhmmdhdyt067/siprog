<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
            'stats' => $this->stats($user),
            'graduation' => $user->role === UserRole::MAHASISWA ? $user->graduation_status : null,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse|JsonResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($request->wantsJson()) {
            $user = $request->user();

            return response()->json([
                'message' => 'Profil berhasil diperbarui.',
                'fields' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '—',
                    'initial' => mb_substr($user->name, 0, 1),
                ],
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse|JsonResponse
    {
        // Admin account is the system owner; it can only be removed directly in the database.
        abort_if($request->user()->role === UserRole::ADMIN, 403, 'Akun admin tidak dapat dihapus.');

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $files = AssignmentSubmission::where('user_id', $user->id)->pluck('file_path')->all();

        Auth::logout();

        $user->delete();
        Storage::disk('local')->delete(array_filter($files));

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Akun berhasil dihapus.',
                'redirect' => '/',
            ]);
        }

        return Redirect::to('/');
    }

    private function stats(User $user): array
    {
        if ($user->role === UserRole::MAHASISWA) {
            $status = $user->graduation_status;

            return [
                [
                    'label' => 'Kehadiran',
                    'value' => $status['attendance_percentage'].'%',
                    'note' => $status['attended_count'].' dari '.$status['total_sessions'].' sesi selesai',
                    'icon' => 'qr',
                    'tone' => 'sky',
                ],
                [
                    'label' => 'Rata-Rata Nilai',
                    'value' => number_format((float) $status['average_score'], 2),
                    'note' => 'Dari seluruh pertemuan',
                    'icon' => 'chart',
                    'tone' => 'emerald',
                ],
                [
                    'label' => 'Sesi Selesai',
                    'value' => $status['total_sessions'],
                    'note' => 'Pertemuan yang sudah ditutup',
                    'icon' => 'calendar',
                    'tone' => 'indigo',
                ],
                [
                    'label' => 'Status Praktikum',
                    'value' => $status['is_passed'] ? 'Lulus' : 'Belum',
                    'note' => $status['is_passed'] ? 'Memenuhi syarat kelulusan' : 'Belum memenuhi syarat',
                    'icon' => $status['is_passed'] ? 'check' : 'clock',
                    'tone' => $status['is_passed'] ? 'lime' : 'amber',
                ],
            ];
        }

        if ($user->role === UserRole::ASISTEN) {
            $classroomIds = $user->assistedClassrooms()->pluck('classrooms.id');
            $sessionIds = PracticumSession::whereIn('classroom_id', $classroomIds)->pluck('id');

            return [
                [
                    'label' => 'Kelas Diampu',
                    'value' => $classroomIds->count(),
                    'note' => 'Kelas praktikum aktif',
                    'icon' => 'building',
                    'tone' => 'sky',
                ],
                [
                    'label' => 'Sesi Diampu',
                    'value' => $sessionIds->count(),
                    'note' => 'Pertemuan pada kelas Anda',
                    'icon' => 'calendar',
                    'tone' => 'indigo',
                ],
                [
                    'label' => 'Presensi Tercatat',
                    'value' => Attendance::whereIn('practicum_session_id', $sessionIds)->count(),
                    'note' => 'Scan mahasiswa di kelas Anda',
                    'icon' => 'qr',
                    'tone' => 'lime',
                ],
                [
                    'label' => 'Tugas Masuk',
                    'value' => AssignmentSubmission::whereHas(
                        'assignment',
                        fn ($query) => $query->whereIn('practicum_session_id', $sessionIds)
                    )->count(),
                    'note' => 'PDF yang perlu dinilai',
                    'icon' => 'doc',
                    'tone' => 'emerald',
                ],
            ];
        }

        return [
            [
                'label' => 'Total Kelas',
                'value' => Classroom::count(),
                'note' => 'Kelas praktikum terdaftar',
                'icon' => 'building',
                'tone' => 'sky',
            ],
            [
                'label' => 'Total Mahasiswa',
                'value' => User::where('role', UserRole::MAHASISWA)->count(),
                'note' => 'Akun mahasiswa',
                'icon' => 'users',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Total Asisten',
                'value' => User::where('role', UserRole::ASISTEN)->count(),
                'note' => 'Asisten pengampu',
                'icon' => 'badge',
                'tone' => 'indigo',
            ],
            [
                'label' => 'Sesi Aktif',
                'value' => PracticumSession::where('status', 'active')->count(),
                'note' => 'QR presensi berjalan',
                'icon' => 'calendar',
                'tone' => 'lime',
            ],
        ];
    }
}
