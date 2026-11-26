<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Konten & Tugas" title="Upload Tugas Mingguan"
            description="Unggah file soal, atur deadline, dan pantau pengumpulan PDF dari seluruh kelas.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $stats['total'] }} tugas</span>
                <span class="chip chip-lime">{{ $stats['open'] }} masih dibuka</span>
                <span class="chip">{{ $stats['submissions'] }} PDF masuk</span>
            </x-slot:meta>
            <x-slot:actions>
                <button type="button" class="btn btn-primary" data-form-modal="assignment-modal"
                    data-action="{{ route('admin.assignments.store') }}" data-method="POST"
                    data-title="Upload Tugas Mingguan" data-submit="Upload & Kirim Notifikasi"
                    data-modal-description="Tugas yang diunggah otomatis mengirim pemberitahuan ke kelas terkait."
                    data-fill="{{ json_encode(['deadline' => now()->addWeek()->format('Y-m-d\TH:i')]) }}">
                    <x-nav-icon name="plus" class="h-3.5 w-3.5" />
                    Upload Tugas
                </button>
            </x-slot:actions>
        </x-page-header>

        <div id="assignments-summary">
            @include('admin.assignments.partials.summary', ['stats' => $stats])
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-1 flex-col gap-2 sm:flex-row">
                    <label class="relative flex-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-nav-icon name="search" class="h-3.5 w-3.5" />
                        </span>
                        <input id="assignment-search" type="search" placeholder="Cari judul tugas atau kelas…"
                            class="field-input mt-0 pl-9" autocomplete="off">
                    </label>
                    <select id="assignment-status" class="field-input mt-0 sm:w-48">
                        <option value="all">Semua deadline</option>
                        <option value="open">Masih dibuka</option>
                        <option value="closed">Sudah ditutup</option>
                    </select>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span id="assignment-count">{{ $assignments->total() }}</span> tugas ditemukan
                </p>
            </div>
        </section>

        <div id="assignments-list" class="space-y-2.5">
            @include('admin.assignments.partials.list', ['assignments' => $assignments])
        </div>
    </div>

    <x-modal name="assignment-modal" max-width="2xl" title="Upload Tugas Mingguan"
        description="Tugas yang diunggah otomatis mengirim pemberitahuan ke kelas terkait.">
        <form data-ajax action="{{ route('admin.assignments.store') }}" method="POST" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="assignment-session">Sesi Pertemuan</label>
                    <select id="assignment-session" name="practicum_session_id" class="field-input" required>
                        <option value="">— Pilih sesi pertemuan —</option>
                        @foreach ($sessions as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->classroom?->name }} — Pertemuan {{ $s->meeting_number }}: {{ $s->title }}
                            </option>
                        @endforeach
                    </select>
                    <p data-error="practicum_session_id" class="field-error hidden"></p>
                </div>

                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="assignment-title">Judul Tugas</label>
                    <input id="assignment-title" name="title" type="text" class="field-input" required
                        placeholder="Contoh: Tugas Sorting & Searching">
                    <p data-error="title" class="field-error hidden"></p>
                </div>

                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="assignment-description">Deskripsi / Instruksi (opsional)</label>
                    <textarea id="assignment-description" name="description" rows="3" class="field-input"
                        placeholder="Instruksi pengerjaan, format file, dan ketentuan pengumpulan…"></textarea>
                    <p data-error="description" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="assignment-file">File Soal (PDF/DOC, maks 10MB)</label>
                    <input id="assignment-file" name="file_soal" type="file" accept=".pdf,.doc,.docx"
                        class="mt-1.5 block w-full cursor-pointer rounded-xl border border-black/10 bg-[#F7F7F4] px-3 py-2.5 text-[12px] text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#0B0B0C] file:px-3 file:py-1.5 file:text-[10px] file:font-bold file:uppercase file:tracking-wider file:text-white">
                    <p class="field-help">File saat ini: <span data-fill-text="file" class="font-mono">—</span>.
                        Kosongkan saat edit untuk mempertahankan file lama.</p>
                    <p data-error="file_soal" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="assignment-deadline">Batas Pengumpulan</label>
                    <input id="assignment-deadline" name="deadline" type="datetime-local" class="field-input" required>
                    <p data-error="deadline" class="field-error hidden"></p>
                </div>
            </div>

            <div class="rounded-2xl border border-[#C8F24D]/50 bg-[#F7FFD9] px-4 py-3 text-[11.5px] leading-relaxed text-[#3F5200]">
                <strong>Notifikasi otomatis.</strong> Pemberitahuan bertipe tugas akan dikirim ke kelas sesi yang
                dipilih.
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="upload" class="h-3.5 w-3.5" />
                    <span data-modal-submit>Upload & Kirim Notifikasi</span>
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterAssignments = function() {
                    const query = ($('#assignment-search').val() || '').toString().trim().toLowerCase();
                    const status = $('#assignment-status').val() || 'all';
                    let visible = 0;

                    $('#assignments-list [data-assignment-card]').each(function() {
                        const card = $(this);
                        const matchesQuery = !query || (card.data('search') || '').includes(query);
                        const matchesStatus = status === 'all' ||
                            (status === 'open' && card.data('open') === 1) ||
                            (status === 'closed' && card.data('open') === 0);
                        const show = matchesQuery && matchesStatus;

                        card.toggle(show);
                        if (show) visible++;
                    });

                    $('#assignment-count').text(visible);
                };

                $('#assignment-search').on('input', filterAssignments);
                $('#assignment-status').on('change', filterAssignments);
                $(document).on('sipro:html-applied', filterAssignments);
            });
        </script>
    @endpush
</x-app-layout>
