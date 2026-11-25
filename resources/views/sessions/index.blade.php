<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Operasional Lab" title="Sesi, QR & Penilaian"
            description="Kelola pertemuan praktikum, tampilkan QR presensi, lalu input nilai mahasiswa dari satu alur kerja.">
            <x-slot:meta>
                <span class="chip chip-sky">Admin & Asisten</span>
                <span class="chip">{{ $stats['total'] }} sesi terdaftar</span>
                <span class="chip {{ $stats['active'] > 0 ? 'chip-lime' : '' }}">
                    {{ $stats['active'] }} sesi aktif
                </span>
            </x-slot:meta>
            <x-slot:actions>
                <button type="button" class="btn btn-primary" data-form-modal="session-modal"
                    data-action="{{ route('sessions.store') }}" data-method="POST" data-title="Buat Sesi Pertemuan"
                    data-submit="Buat & Tampilkan QR"
                    data-modal-description="Sesi baru langsung aktif dengan QR presensi yang siap diproyeksikan."
                    data-fill="{{ json_encode(['session_date' => date('Y-m-d'), 'meeting_number' => 1]) }}">
                    <x-nav-icon name="plus" class="h-3.5 w-3.5" />
                    Sesi Baru
                </button>
            </x-slot:actions>
        </x-page-header>

        <div id="sessions-summary">
            @include('sessions.partials.summary', ['stats' => $stats])
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-1 flex-col gap-2 sm:flex-row">
                    <label class="relative flex-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-nav-icon name="search" class="h-3.5 w-3.5" />
                        </span>
                        <input id="session-search" type="search" placeholder="Cari judul, kelas, atau pertemuan…"
                            class="field-input mt-0 pl-9" autocomplete="off">
                    </label>
                    <select id="session-status" class="field-input mt-0 sm:w-48">
                        <option value="all">Semua status</option>
                        <option value="active">Aktif</option>
                        <option value="finished">Selesai</option>
                    </select>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span id="session-count">{{ $sessions->total() }}</span> sesi ditemukan
                </p>
            </div>
        </section>

        <div id="sessions-list" class="space-y-2.5">
            @include('sessions.partials.list', ['sessions' => $sessions])
        </div>
    </div>

    <x-modal name="session-modal" max-width="2xl" title="Buat Sesi Pertemuan"
        description="Sesi baru langsung aktif dengan QR presensi yang siap diproyeksikan.">
        <form data-ajax action="{{ route('sessions.store') }}" method="POST" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="session-classroom">Kelas Praktikum</label>
                    <select id="session-classroom" name="classroom_id" class="field-input" required>
                        @foreach ($classrooms as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->course_name }}</option>
                        @endforeach
                    </select>
                    <p data-error="classroom_id" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="session-meeting">Pertemuan Ke-</label>
                    <input id="session-meeting" name="meeting_number" type="number" min="1" max="30"
                        class="field-input" required placeholder="1">
                    <p data-error="meeting_number" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="session-date">Tanggal</label>
                    <input id="session-date" name="session_date" type="date" class="field-input" required>
                    <p data-error="session_date" class="field-error hidden"></p>
                </div>

                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="session-title">Judul Materi</label>
                    <input id="session-title" name="title" type="text" class="field-input" required
                        placeholder="Contoh: Implementasi Sorting & Searching">
                    <p data-error="title" class="field-error hidden"></p>
                </div>

                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="session-description">Deskripsi Materi (opsional)</label>
                    <textarea id="session-description" name="description" rows="3" class="field-input"
                        placeholder="Ringkasan materi atau instruksi awal pertemuan…"></textarea>
                    <p data-error="description" class="field-error hidden"></p>
                </div>
            </div>

            <div class="rounded-2xl border border-[#0A66C8]/15 bg-[#EAF4FF]/60 px-4 py-3 text-[11.5px] leading-relaxed text-[#0A4A8F]">
                <strong>QR otomatis aktif.</strong> Token presensi dibuat saat sesi disimpan dan dapat diperbarui dari
                halaman proyektor.
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <span data-modal-submit>Buat & Tampilkan QR</span>
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterSessions = function() {
                    const query = ($('#session-search').val() || '').toString().trim().toLowerCase();
                    const status = $('#session-status').val() || 'all';
                    let visible = 0;

                    $('#sessions-list [data-session-card]').each(function() {
                        const card = $(this);
                        const matchesQuery = !query || (card.data('search') || '').includes(query);
                        const matchesStatus = status === 'all' || card.data('status') === status;
                        const show = matchesQuery && matchesStatus;

                        card.toggle(show);
                        if (show) visible++;
                    });

                    $('#session-count').text(visible);
                };

                $('#session-search').on('input', filterSessions);
                $('#session-status').on('change', filterSessions);
                $(document).on('sipro:html-applied', filterSessions);
            });
        </script>
    @endpush
</x-app-layout>
