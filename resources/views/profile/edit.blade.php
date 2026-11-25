<x-app-layout>
    @php
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->map(fn ($word) => mb_substr($word, 0, 1))
            ->take(2)
            ->implode('');

        $roleStyles = match ($user->role) {
            \App\Enums\UserRole::ADMIN => 'chip-amber',
            \App\Enums\UserRole::ASISTEN => 'chip-emerald',
            default => 'chip-sky',
        };

        $classLabel = match ($user->role) {
            \App\Enums\UserRole::ASISTEN => $user->assistedClassrooms->pluck('name')->implode(', '),
            \App\Enums\UserRole::ADMIN => 'Seluruh kelas',
            default => $user->classroom?->name,
        };
    @endphp

    <div class="mx-auto max-w-6xl space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Akun" title="Profil Akun"
            description="Kelola identitas, keamanan akun, dan pantau status praktikum Anda.">
            <x-slot:meta>
                <span class="chip {{ $roleStyles }}">{{ $user->role?->label() ?? 'Pengguna' }}</span>
                <span class="chip">Bergabung {{ $user->created_at?->locale('id')->translatedFormat('d M Y') ?? '—' }}</span>
            </x-slot:meta>
            <x-slot:actions>
                <button type="button" class="btn btn-light" data-form-modal="profile-modal"
                    data-fill="{{ json_encode(['name' => $user->name, 'email' => $user->email, 'phone' => $user->phone]) }}">
                    <x-nav-icon name="edit" class="h-3.5 w-3.5" />
                    Edit Profil
                </button>
                <button type="button" class="btn btn-primary" data-form-modal="password-modal">
                    <x-nav-icon name="user" class="h-3.5 w-3.5" />
                    Ubah Kata Sandi
                </button>
            </x-slot:actions>
        </x-page-header>

        @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[12px] font-medium text-emerald-800">
                {{ session('status') === 'profile-updated' ? 'Profil berhasil diperbarui.' : 'Kata sandi berhasil diperbarui.' }}
            </div>
        @endif

        <section class="card relative overflow-hidden px-5 py-5 sm:px-6 sm:py-6">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -right-16 -top-24 h-60 w-60 rounded-full bg-[#EAF4FF] blur-3xl"></div>
                <div class="absolute -bottom-24 right-28 h-44 w-44 rounded-full bg-[#F7FFD9] blur-3xl"></div>
            </div>

            <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-center">
                    <div
                        class="grid h-[4.5rem] w-[4.5rem] shrink-0 place-items-center rounded-[26px] bg-[linear-gradient(140deg,#0A66C8,#6ACBFA)] text-[1.6rem] font-extrabold text-white shadow-[0_18px_40px_-22px_rgba(10,102,200,.7)]">
                        <span data-profile-field="initial">{{ $initials ?: 'U' }}</span>
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 data-profile-field="name" class="truncate text-[1.35rem] font-bold tracking-[-0.03em]">
                                {{ $user->name }}</h2>
                            <span class="chip {{ $roleStyles }}">{{ $user->role?->label() }}</span>
                        </div>
                        <p data-profile-field="email" class="mt-1 truncate text-[12px] text-slate-500">{{ $user->email }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[10.5px] font-semibold text-slate-500">
                            <span class="font-mono">{{ $user->nim_nip }}</span>
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span data-profile-field="phone">{{ $user->phone ?: 'No. HP belum diisi' }}</span>
                        </div>
                    </div>
                </div>

                <dl class="grid grid-cols-2 gap-2 sm:min-w-[24rem] sm:grid-cols-2">
                    <div class="rounded-2xl border border-black/5 bg-[#F9F9F6] px-3.5 py-3">
                        <dt class="text-[8.5px] font-extrabold uppercase tracking-[0.16em] text-slate-500">NIM / NIP</dt>
                        <dd class="mt-1 truncate font-mono text-[12px] font-bold">{{ $user->nim_nip ?? '—' }}</dd>
                    </div>
                    <div class="rounded-2xl border border-black/5 bg-[#F9F9F6] px-3.5 py-3">
                        <dt class="text-[8.5px] font-extrabold uppercase tracking-[0.16em] text-slate-500">
                            {{ $user->role === \App\Enums\UserRole::ASISTEN ? 'Kelas Diampu' : 'Kelas' }}
                        </dt>
                        <dd class="mt-1 truncate text-[12px] font-bold">{{ $classLabel ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ($stats as $card)
                <x-stat-card :label="$card['label']" :value="$card['value']" :note="$card['note']"
                    :icon="$card['icon']" :tone="$card['tone']" />
            @endforeach
        </div>

        @if ($graduation)
            <section class="card px-5 py-5 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Kelulusan</p>
                        <h2 class="mt-1 text-[16px] font-bold tracking-[-0.02em]">Status Praktikum</h2>
                    </div>
                    <span class="chip {{ $graduation['is_passed'] ? 'chip-emerald' : 'chip-rose' }}">
                        {{ $graduation['label'] }}
                    </span>
                </div>

                <div class="mt-4 grid gap-4 lg:grid-cols-[1.2fr_1fr]">
                    <div class="rounded-2xl border border-black/5 bg-[#F9F9F6] px-4 py-4">
                        <div class="flex items-end justify-between gap-3">
                            <div>
                                <p class="text-[9px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Progres
                                    kehadiran</p>
                                <p class="mt-1 text-[1.6rem] font-extrabold leading-none tracking-[-0.04em]">
                                    {{ $graduation['attendance_percentage'] }}%</p>
                            </div>
                            <p class="text-[11px] font-semibold text-slate-500">
                                {{ $graduation['attended_count'] }} / {{ $graduation['total_sessions'] }} sesi</p>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-black/5">
                            <div class="h-full rounded-full bg-[linear-gradient(90deg,#0A66C8,#6ACBFA)]"
                                style="width: {{ min(100, (float) $graduation['attendance_percentage']) }}%"></div>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-[10px] font-semibold text-slate-500">
                            <span class="chip {{ $graduation['attendance_percentage'] >= 75 ? 'chip-emerald' : 'chip-rose' }}">
                                Kehadiran ≥ 75%
                            </span>
                            <span class="chip {{ $graduation['average_score'] >= 70 ? 'chip-emerald' : 'chip-rose' }}">
                                Rata-rata ≥ 70
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-2xl border border-black/5 bg-white px-3.5 py-3">
                            <p class="text-[8.5px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Rata-Rata
                            </p>
                            <p class="mt-1 text-[1.3rem] font-extrabold leading-none">
                                {{ number_format((float) $graduation['average_score'], 2) }}</p>
                        </div>
                        <div class="rounded-2xl border border-black/5 bg-white px-3.5 py-3">
                            <p class="text-[8.5px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Sesi
                                Selesai</p>
                            <p class="mt-1 text-[1.3rem] font-extrabold leading-none">
                                {{ $graduation['total_sessions'] }}</p>
                        </div>
                        <div class="col-span-2 rounded-2xl border border-black/5 bg-white px-3.5 py-3">
                            <p class="text-[8.5px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Syarat
                                Praktikum</p>
                            <p class="mt-1 text-[11px] leading-relaxed text-slate-600">Kehadiran minimal 75% dan
                                rata-rata nilai minimal 70.00 dari seluruh pertemuan.</p>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="grid gap-4 lg:grid-cols-2">
            <section class="card px-5 py-5 sm:px-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Identitas</p>
                        <h2 class="mt-1 text-[16px] font-bold tracking-[-0.02em]">Informasi Akun</h2>
                    </div>
                    <button type="button" class="row-action" data-form-modal="profile-modal"
                        data-fill="{{ json_encode(['name' => $user->name, 'email' => $user->email, 'phone' => $user->phone]) }}">
                        <x-nav-icon name="edit" class="h-3 w-3" />
                        Ubah
                    </button>
                </div>

                <dl class="mt-4 divide-y divide-black/5 text-[12px]">
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <dt class="text-slate-500">Nama lengkap</dt>
                        <dd data-profile-field="name" class="max-w-[60%] truncate text-right font-semibold">
                            {{ $user->name }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <dt class="text-slate-500">Email</dt>
                        <dd data-profile-field="email" class="max-w-[60%] truncate text-right font-semibold">
                            {{ $user->email }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <dt class="text-slate-500">No. HP</dt>
                        <dd data-profile-field="phone" class="max-w-[60%] truncate text-right font-semibold">
                            {{ $user->phone ?: '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <dt class="text-slate-500">NIM / NIP</dt>
                        <dd class="font-mono text-[11px] font-semibold">{{ $user->nim_nip ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <dt class="text-slate-500">{{ $user->role === \App\Enums\UserRole::ASISTEN ? 'Kelas diampu' : 'Kelas' }}
                        </dt>
                        <dd class="max-w-[60%] truncate text-right font-semibold">{{ $classLabel ?: '—' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="card px-5 py-5 sm:px-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Keamanan</p>
                        <h2 class="mt-1 text-[16px] font-bold tracking-[-0.02em]">Kata Sandi & Sesi</h2>
                    </div>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#F7FFD9] text-[#3F5200]">
                        <x-nav-icon name="user" class="h-4 w-4" />
                    </span>
                </div>

                <p class="mt-3 text-[11.5px] leading-relaxed text-slate-500">
                    Gunakan kata sandi yang panjang dan unik. Jika perangkat dipakai bersama, selalu akhiri sesi setelah
                    selesai.
                </p>

                <div class="mt-4 space-y-2">
                    <button type="button" class="row-action w-full justify-between" data-form-modal="password-modal">
                        <span class="inline-flex items-center gap-1.5">
                            <x-nav-icon name="user" class="h-3 w-3" />
                            Ubah kata sandi
                        </span>
                        <x-nav-icon name="arrow-right" class="h-3 w-3" />
                    </button>
                    <button type="button" class="row-action w-full justify-between" data-logout>
                        <span class="inline-flex items-center gap-1.5">
                            <x-nav-icon name="logout" class="h-3 w-3" />
                            Keluar dari akun
                        </span>
                        <x-nav-icon name="arrow-right" class="h-3 w-3" />
                    </button>
                </div>

                <div class="mt-4 rounded-2xl border border-[#0A66C8]/15 bg-[#EAF4FF]/60 px-4 py-3 text-[10.5px] leading-relaxed text-[#0A4A8F]">
                    Perubahan email akan menonaktifkan verifikasi lama hingga email baru diverifikasi kembali.
                </div>
            </section>
        </div>

        @unless ($user->role === \App\Enums\UserRole::ADMIN)
        <section class="card border-rose-200 px-5 py-5 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-rose-500">Zona Berbahaya</p>
                    <h2 class="mt-1 text-[16px] font-bold tracking-[-0.02em] text-rose-600">Hapus Akun</h2>
                    <p class="mt-1 max-w-xl text-[11.5px] leading-relaxed text-slate-500">
                        Tindakan ini permanen. Seluruh data akun, presensi, nilai, dan tugas yang terkait akan dihapus.
                    </p>
                </div>
                <button type="button" class="btn btn-danger-soft" data-form-modal="delete-account-modal">
                    <x-nav-icon name="trash" class="h-3.5 w-3.5" />
                    Hapus Akun
                </button>
            </div>
        </section>
        @endunless
    </div>

    <x-modal name="profile-modal" max-width="lg" title="Edit Profil"
        description="Perbarui nama, email, dan nomor HP yang tampil di akun Anda.">
        <form data-ajax action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('patch')

            <div data-field-group>
                <label class="field-label" for="profile-name">Nama Lengkap</label>
                <input id="profile-name" name="name" type="text" value="{{ old('name', $user->name) }}"
                    class="field-input" required autocomplete="name">
                <p class="field-help">Nama lengkap dipakai sebagai username saat login. Perubahan nama akan mengubah
                    username Anda.</p>
                <p data-error="name" class="field-error hidden"></p>
            </div>

            <div data-field-group>
                <label class="field-label" for="profile-email">Email</label>
                <input id="profile-email" name="email" type="email" value="{{ old('email', $user->email) }}"
                    class="field-input" required autocomplete="username">
                <p data-error="email" class="field-error hidden"></p>
            </div>

            <div data-field-group>
                <label class="field-label" for="profile-phone">No. HP (opsional)</label>
                <input id="profile-phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                    class="field-input" autocomplete="tel" placeholder="08xxxxxxxxxx">
                <p data-error="phone" class="field-error hidden"></p>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="check" class="h-3.5 w-3.5" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-modal>

    <x-modal name="password-modal" max-width="lg" title="Ubah Kata Sandi"
        description="Masukkan kata sandi saat ini, lalu tentukan kata sandi baru.">
        <form data-ajax action="{{ route('password.update') }}" method="POST" class="space-y-4" data-reset-on-success>
            @csrf
            @method('put')

            <div data-field-group>
                <label class="field-label" for="password-current">Kata Sandi Saat Ini</label>
                <input id="password-current" name="current_password" type="password" class="field-input"
                    autocomplete="current-password" required>
                <p data-error="current_password" class="field-error hidden"></p>
            </div>

            <div data-field-group>
                <label class="field-label" for="password-new">Kata Sandi Baru</label>
                <input id="password-new" name="password" type="password" class="field-input"
                    autocomplete="new-password" required>
                <p data-error="password" class="field-error hidden"></p>
            </div>

            <div data-field-group>
                <label class="field-label" for="password-confirm">Konfirmasi Kata Sandi Baru</label>
                <input id="password-confirm" name="password_confirmation" type="password" class="field-input"
                    autocomplete="new-password" required>
                <p data-error="password_confirmation" class="field-error hidden"></p>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="check" class="h-3.5 w-3.5" />
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </x-modal>

    @unless ($user->role === \App\Enums\UserRole::ADMIN)
    <x-modal name="delete-account-modal" max-width="lg" title="Hapus Akun Permanen"
        description="Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi untuk mengonfirmasi.">
        <form data-ajax action="{{ route('profile.destroy') }}" method="POST" class="space-y-4">
            @csrf
            @method('delete')

            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-[11.5px] leading-relaxed text-rose-700">
                Seluruh data akun, riwayat presensi, nilai, dan tugas akan dihapus permanen.
            </div>

            <div data-field-group>
                <label class="field-label" for="delete-password">Kata Sandi</label>
                <input id="delete-password" name="password" type="password" class="field-input"
                    autocomplete="current-password" placeholder="Kata sandi Anda" required>
                <p data-error="password" class="field-error hidden"></p>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-danger">
                    <x-nav-icon name="trash" class="h-3.5 w-3.5" />
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
    @endunless

</x-app-layout>
