<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Data Master" title="Data Asisten Praktikum"
            description="Kelola akun asisten dan kelas yang diampu untuk mengatur akses sesi, presensi, dan penilaian.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $stats['users'] }} asisten</span>
                <span class="chip chip-amber">{{ $stats['without_class'] }} belum mengampu</span>
            </x-slot:meta>
            <x-slot:actions>
                <button type="button" class="btn btn-primary" data-form-modal="assistant-modal"
                    data-action="{{ route('admin.master.users.store') }}" data-method="POST"
                    data-title="Tambah Asisten" data-submit="Simpan Asisten"
                    data-modal-description="Pilih satu atau lebih kelas yang diampu asisten ini.">
                    <x-nav-icon name="plus" class="h-3.5 w-3.5" />
                    Tambah Asisten
                </button>
            </x-slot:actions>
        </x-page-header>

        <div id="assistants-summary">
            @include('admin.master.partials.user-summary', ['stats' => $stats, 'role' => 'asisten'])
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <form method="GET" action="{{ route('admin.master.assistants') }}"
                class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label class="relative flex-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-nav-icon name="search" class="h-3.5 w-3.5" />
                    </span>
                    <input name="q" value="{{ $search }}" type="search"
                        placeholder="Cari nama, NIP, atau email asisten…" class="field-input mt-0 pl-9"
                        autocomplete="off">
                </label>
                <button type="submit" class="btn btn-primary sm:w-auto">
                    <x-nav-icon name="search" class="h-3.5 w-3.5" />
                    Cari
                </button>
                @if ($search !== '')
                    <a href="{{ route('admin.master.assistants') }}" class="btn btn-light sm:w-auto">
                        <x-nav-icon name="x" class="h-3.5 w-3.5" />
                        Reset
                    </a>
                @endif
            </form>
            @if ($search !== '')
                <p class="mt-3 border-t border-black/5 pt-3 text-[11px] text-slate-500">
                    Hasil pencarian untuk <strong class="text-[#0B0B0C]">“{{ $search }}”</strong> ·
                    {{ $assistants->total() }} asisten ditemukan.
                </p>
            @endif
        </section>

        <div id="assistants-list" class="space-y-2.5">
            @include('admin.master.partials.assistant-list', ['assistants' => $assistants])
        </div>
    </div>

    <x-modal name="assistant-modal" max-width="2xl" title="Tambah Asisten"
        description="Pilih satu atau lebih kelas yang diampu asisten ini.">
        <form data-ajax action="{{ route('admin.master.users.store') }}" method="POST" class="space-y-4">
            <input type="hidden" name="role" value="asisten">

            <div class="grid gap-4 sm:grid-cols-2">
                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="assistant-name">Nama Lengkap</label>
                    <input id="assistant-name" name="name" type="text" class="field-input" required
                        placeholder="Nama lengkap asisten">
                    <p class="field-help">Nama lengkap menjadi username login dan harus unik.</p>
                    <p data-error="name" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="assistant-nip">NIP / ID Asisten</label>
                    <input id="assistant-nip" name="nim_nip" type="text" class="field-input" required
                        placeholder="1987654321">
                    <p data-error="nim_nip" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="assistant-phone">No. HP (opsional)</label>
                    <input id="assistant-phone" name="phone" type="text" class="field-input"
                        placeholder="08xxxxxxxxxx">
                    <p data-error="phone" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="assistant-email">Email</label>
                    <input id="assistant-email" name="email" type="email" class="field-input" required
                        placeholder="asisten@kampus.ac.id">
                    <p data-error="email" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="assistant-password">Kata Sandi</label>
                    <input id="assistant-password" name="password" type="password" class="field-input"
                        autocomplete="new-password" placeholder="Minimal 6 karakter">
                    <p class="field-help">Wajib saat menambah akun. Gunakan NIM/NIP sebagai kata sandi awal.
                        Kosongkan saat edit jika tidak diubah.</p>
                    <p data-error="password" class="field-error hidden"></p>
                </div>
            </div>

            <div data-field-group>
                <p class="field-label">Kelas yang Diampu</p>
                <div class="mt-1.5 grid gap-2 sm:grid-cols-2">
                    @foreach ($classrooms as $c)
                        <label
                            class="flex cursor-pointer items-start gap-2.5 rounded-xl border border-black/10 bg-[#F7F7F4] px-3 py-2.5 transition hover:border-[#0A66C8]/30 hover:bg-white">
                            <input type="checkbox" name="assisted_classroom_ids[]" value="{{ $c->id }}"
                                class="mt-0.5 rounded border-black/20 text-[#0A66C8] focus:ring-[#0A66C8]/30">
                            <span class="min-w-0">
                                <span class="block text-[12px] font-bold leading-tight">{{ $c->name }}</span>
                                <span class="mt-0.5 block text-[10px] leading-snug text-slate-500">
                                    {{ $c->course_name }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <p data-error="assisted_classroom_ids" class="field-error hidden"></p>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="check" class="h-3.5 w-3.5" />
                    <span data-modal-submit>Simpan Asisten</span>
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
