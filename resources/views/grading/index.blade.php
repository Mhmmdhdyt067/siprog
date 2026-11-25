<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Penilaian Praktikum" :title="'Nilai Pertemuan ' . $session->meeting_number"
            :description="($session->classroom?->name ?? 'Kelas') . ' · ' . ($session->title ?? '') . '. Nilai akhir dihitung otomatis dari pre-test, tugas, dan nilai tambahan.'">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $session->classroom?->name }}</span>
                <span class="chip">{{ $session->session_date?->locale('id')->translatedFormat('l, d M Y') }}</span>
                <span class="chip {{ $session->status === 'active' ? 'chip-lime' : 'chip-emerald' }}">
                    {{ $session->status === 'active' ? 'Sesi Aktif' : 'Sesi Selesai' }}
                </span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('sessions.index') }}" class="btn btn-light">
                    <x-nav-icon name="arrow-left" class="h-3.5 w-3.5" />
                    Daftar Sesi
                </a>
                <a href="{{ route('sessions.qr', $session) }}" class="btn btn-primary">
                    <x-nav-icon name="qr" class="h-3.5 w-3.5" />
                    QR Presensi
                </a>
            </x-slot:actions>
        </x-page-header>

        <div id="grading-summary">
            @include('grading.partials.summary', ['stats' => $stats])
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-1 flex-col gap-2 sm:flex-row">
                    <label class="relative flex-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-nav-icon name="search" class="h-3.5 w-3.5" />
                        </span>
                        <input id="grading-search" type="search" placeholder="Cari nama atau NIM mahasiswa…"
                            class="field-input mt-0 pl-9" autocomplete="off">
                    </label>
                    <select id="grading-status" class="field-input mt-0 sm:w-48">
                        <option value="all">Semua mahasiswa</option>
                        <option value="pending">Belum dinilai</option>
                        <option value="graded">Sudah dinilai</option>
                    </select>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span id="grading-count">{{ $students->count() }}</span> mahasiswa ditampilkan
                </p>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-black/5 pt-3 text-[10px] text-slate-500">
                <span class="chip chip-sky">Pre-Test 40%</span>
                <span class="chip chip-indigo">Tugas 60%</span>
                <span class="chip chip-lime">+ Nilai Tambahan</span>
                <span class="ml-auto hidden text-[10px] sm:inline">Klik <strong class="text-[#0B0B0C]">Nilai</strong> pada
                    baris mahasiswa.</span>
            </div>
        </section>

        <div id="grading-list">
            @include('grading.partials.list', ['students' => $students, 'session' => $session])
        </div>
    </div>

    <x-modal name="grade-modal" max-width="lg" title="Input Nilai"
        description="Nilai akhir dihitung otomatis: 40% pre-test, 60% tugas, ditambah nilai tambahan.">
        <form data-ajax action="" method="POST" class="space-y-4">
            <div class="flex items-center gap-3 rounded-2xl border border-black/5 bg-[#F9F9F6] px-4 py-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#0B0B0C] text-[#C8F24D]">
                    <x-nav-icon name="user" class="h-4 w-4" />
                </span>
                <div class="min-w-0">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Mahasiswa</p>
                    <p data-fill-text="student" class="truncate text-[13px] font-bold">—</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div data-field-group>
                    <label class="field-label" for="grade-pretest">Pre-Test (40%)</label>
                    <input id="grade-pretest" name="pretest_score" type="number" min="0" max="100"
                        step="0.01" class="field-input" required>
                    <p data-error="pretest_score" class="field-error hidden"></p>
                </div>
                <div data-field-group>
                    <label class="field-label" for="grade-assignment">Tugas (60%)</label>
                    <input id="grade-assignment" name="assignment_score" type="number" min="0" max="100"
                        step="0.01" class="field-input" required>
                    <p data-error="assignment_score" class="field-error hidden"></p>
                </div>
                <div data-field-group>
                    <label class="field-label" for="grade-extra">Tambahan</label>
                    <input id="grade-extra" name="extra_score" type="number" min="0" max="100" step="0.01"
                        class="field-input" required>
                    <p data-error="extra_score" class="field-error hidden"></p>
                </div>
            </div>

            <div data-field-group>
                <label class="field-label" for="grade-notes">Catatan (opsional)</label>
                <textarea id="grade-notes" name="notes" rows="3" class="field-input"
                    placeholder="Catatan untuk mahasiswa…"></textarea>
                <p data-error="notes" class="field-error hidden"></p>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="check" class="h-3.5 w-3.5" />
                    <span data-modal-submit>Simpan Nilai</span>
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterRows = function() {
                    const query = ($('#grading-search').val() || '').toString().trim().toLowerCase();
                    const status = $('#grading-status').val() || 'all';
                    let visible = 0;

                    $('#grading-list [data-grade-row]').each(function() {
                        const row = $(this);
                        const matchesQuery = !query || (row.data('search') || '').includes(query);
                        const matchesStatus = status === 'all' ||
                            (status === 'graded' && row.data('graded') === 1) ||
                            (status === 'pending' && row.data('graded') === 0);
                        const show = matchesQuery && matchesStatus;

                        row.toggle(show);
                        if (show) visible++;
                    });

                    $('#grading-count').text(visible);
                };

                $('#grading-search').on('input', filterRows);
                $('#grading-status').on('change', filterRows);
                $(document).on('sipro:html-applied', filterRows);
            });
        </script>
    @endpush
</x-app-layout>
