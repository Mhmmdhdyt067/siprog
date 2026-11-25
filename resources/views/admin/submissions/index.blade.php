<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Pemeriksaan Tugas" title="Periksa Tugas Mingguan"
            description="Pantau siapa yang sudah mengumpulkan, buka pratinjau PDF, unduh berkas, dan isi nilai tugas langsung dari satu halaman.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $stats['assignments'] }} tugas</span>
                <span class="chip chip-lime">{{ $stats['submissions'] }} PDF masuk</span>
                <span class="chip">{{ $stats['graded'] }} nilai tersimpan</span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('admin.assignments.index') }}" class="btn btn-primary">
                    <x-nav-icon name="upload" class="h-3.5 w-3.5" />
                    Upload Tugas
                </a>
            </x-slot:actions>
        </x-page-header>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <x-stat-card label="Tugas Terunggah" :value="$stats['assignments']" note="Tugas mingguan aktif" icon="doc"
                tone="sky" />
            <x-stat-card label="Ada Pengumpulan" :value="$stats['with_submissions']" note="Tugas dengan PDF masuk"
                icon="upload" tone="lime" />
            <x-stat-card label="PDF Masuk" :value="$stats['submissions']" note="Dari seluruh kelas" icon="eye"
                tone="emerald" />
            <x-stat-card label="Sudah Dinilai" :value="$stats['graded']" note="Nilai tugas tersimpan" icon="chart"
                tone="indigo" />
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-1 flex-col gap-2 sm:flex-row">
                    <label class="relative flex-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-nav-icon name="search" class="h-3.5 w-3.5" />
                        </span>
                        <input id="review-search" type="search" placeholder="Cari judul tugas, kelas, atau pertemuan…"
                            class="field-input mt-0 pl-9" autocomplete="off">
                    </label>
                    <select id="review-status" class="field-input mt-0 sm:w-52">
                        <option value="all">Semua tugas</option>
                        <option value="submitted">Sudah ada pengumpulan</option>
                        <option value="empty">Belum ada pengumpulan</option>
                    </select>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span id="review-count">{{ $assignments->count() }}</span> tugas ditampilkan
                </p>
            </div>
        </section>

        <div id="review-list" class="space-y-2.5">
            @include('admin.submissions.partials.assignment-list', [
                'assignments' => $assignments,
                'studentCounts' => $studentCounts,
                'gradedCounts' => $gradedCounts,
            ])
        </div>

        @if ($assignments->isNotEmpty())
            <div id="review-empty" class="card hidden px-5 py-12 text-center">
                <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#F5F5F2] text-slate-500">
                    <x-nav-icon name="search" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-[14px] font-bold">Tidak ada tugas yang cocok</p>
                <p class="mt-1 text-[12px] text-slate-500">Ubah kata kunci atau pilihan status pengumpulan.</p>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterAssignments = function() {
                    const query = ($('#review-search').val() || '').toString().trim().toLowerCase();
                    const status = $('#review-status').val() || 'all';
                    let visible = 0;

                    $('#review-list [data-review-card]').each(function() {
                        const card = $(this);
                        const matchesQuery = !query || (card.data('search') || '').includes(query);
                        const matchesStatus = status === 'all' ||
                            (status === 'submitted' && card.data('has-submissions') === 1) ||
                            (status === 'empty' && card.data('has-submissions') === 0);
                        const show = matchesQuery && matchesStatus;

                        card.toggle(show);
                        if (show) visible++;
                    });

                    $('#review-count').text(visible);
                    $('#review-empty').toggle(visible === 0);
                };

                $('#review-search').on('input', filterAssignments);
                $('#review-status').on('change', filterAssignments);
            });
        </script>
    @endpush
</x-app-layout>
