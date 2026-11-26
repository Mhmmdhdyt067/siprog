<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Data Master" title="Data Mahasiswa"
            description="Kelola akun mahasiswa, penempatan kelas, dan kontak yang dipakai untuk presensi serta penilaian.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $stats['users'] }} mahasiswa</span>
                <span class="chip chip-amber">{{ $stats['without_class'] }} belum ada kelas</span>
            </x-slot:meta>
            <x-slot:actions>
                <button type="button" class="btn btn-primary" data-form-modal="student-modal"
                    data-action="{{ route('admin.master.users.store') }}" data-method="POST"
                    data-title="Tambah Mahasiswa" data-submit="Simpan Mahasiswa"
                    data-modal-description="Akun mahasiswa langsung dapat digunakan untuk login dan presensi.">
                    <x-nav-icon name="plus" class="h-3.5 w-3.5" />
                    Tambah Mahasiswa
                </button>
            </x-slot:actions>
        </x-page-header>

        <div id="students-summary">
            @include('admin.master.partials.user-summary', ['stats' => $stats, 'role' => 'mahasiswa'])
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <form method="GET" action="{{ route('admin.master.students') }}"
                class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label class="relative flex-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-nav-icon name="search" class="h-3.5 w-3.5" />
                    </span>
                    <input name="q" value="{{ $search }}" type="search"
                        placeholder="Cari nama, NIM, atau email mahasiswa…" class="field-input mt-0 pl-9"
                        autocomplete="off">
                </label>
                <button type="submit" class="btn btn-primary sm:w-auto">
                    <x-nav-icon name="search" class="h-3.5 w-3.5" />
                    Cari
                </button>
                @if ($search !== '')
                    <a href="{{ route('admin.master.students') }}" class="btn btn-light sm:w-auto">
                        <x-nav-icon name="x" class="h-3.5 w-3.5" />
                        Reset
                    </a>
                @endif
            </form>
            @if ($search !== '')
                <p class="mt-3 border-t border-black/5 pt-3 text-[11px] text-slate-500">
                    Hasil pencarian untuk <strong class="text-[#0B0B0C]">“{{ $search }}”</strong> ·
                    {{ $students->total() }} mahasiswa ditemukan.
                </p>
            @endif
        </section>

        <div id="students-list" class="space-y-2.5">
            @include('admin.master.partials.student-list', ['students' => $students])
        </div>
    </div>

    <x-modal name="student-modal" max-width="2xl" title="Tambah Mahasiswa"
        description="Akun mahasiswa langsung dapat digunakan untuk login dan presensi.">
        <form data-ajax action="{{ route('admin.master.users.store') }}" method="POST" class="space-y-4">
            <input type="hidden" name="role" value="mahasiswa">

            <div class="grid gap-4 sm:grid-cols-2">
                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="student-name">Nama Lengkap</label>
                    <input id="student-name" name="name" type="text" class="field-input" required
                        placeholder="Nama sesuai KTM">
                    <p class="field-help">Nama lengkap menjadi username login dan harus unik.</p>
                    <p data-error="name" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="student-nim">NIM</label>
                    <input id="student-nim" name="nim_nip" type="text" class="field-input" required
                        placeholder="22101001">
                    <p data-error="nim_nip" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="student-class">Kelas</label>
                    <select id="student-class" name="classroom_id" class="field-input">
                        <option value="">— Belum ditempatkan —</option>
                        @foreach ($classrooms as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->course_name }}</option>
                        @endforeach
                    </select>
                    <p data-error="classroom_id" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="student-email">Email</label>
                    <input id="student-email" name="email" type="email" class="field-input" required
                        placeholder="nama@student.ac.id">
                    <p data-error="email" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="student-phone">No. HP (opsional)</label>
                    <input id="student-phone" name="phone" type="text" class="field-input"
                        placeholder="08xxxxxxxxxx">
                    <p data-error="phone" class="field-error hidden"></p>
                </div>

                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="student-password">Kata Sandi</label>
                    <input id="student-password" name="password" type="password" class="field-input"
                        autocomplete="new-password" placeholder="Minimal 6 karakter">
                    <p class="field-help">Wajib diisi saat menambah akun. Gunakan NIM sebagai kata sandi awal. Kosongkan
                        saat edit jika tidak ingin menggantinya.</p>
                    <p data-error="password" class="field-error hidden"></p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="check" class="h-3.5 w-3.5" />
                    <span data-modal-submit>Simpan Mahasiswa</span>
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
