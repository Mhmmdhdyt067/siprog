<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Data Master" title="Nilai & Export Excel"
            description="Unduh rekap nilai praktikum dalam format Excel. Gunakan rekap keseluruhan untuk laporan lengkap, atau pilih satu pertemuan untuk pemeriksaan detail.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $stats['classrooms'] }} kelas</span>
                <span class="chip">{{ $stats['sessions'] }} pertemuan</span>
                <span class="chip">{{ $stats['students'] }} mahasiswa</span>
                <span class="chip">{{ $stats['grades'] }} nilai terinput</span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('admin.export.overall') }}" class="btn btn-primary">
                    <x-nav-icon name="download" class="h-3.5 w-3.5" />
                    Unduh Rekap Semua Kelas
                </a>
            </x-slot:actions>
        </x-page-header>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <x-stat-card label="Kelas Praktikum" :value="$stats['classrooms']" note="Kelas dalam cakupan export"
                icon="building" tone="sky" />
            <x-stat-card label="Pertemuan" :value="$stats['sessions']" note="Bisa diunduh per sesi" icon="calendar"
                tone="lime" />
            <x-stat-card label="Mahasiswa" :value="$stats['students']" note="Terdaftar di semua kelas" icon="users"
                tone="indigo" />
            <x-stat-card label="Nilai Terinput" :value="$stats['grades']" note="Tersimpan di seluruh pertemuan"
                icon="chart" tone="emerald" />
        </div>

        <section id="rekap-keseluruhan" class="card overflow-hidden">
            <div class="grid lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
                <div class="relative isolate overflow-hidden p-5 sm:p-6">
                    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true">
                        <div class="absolute -left-16 -top-20 h-52 w-52 rounded-full bg-[#EAF4FF] blur-2xl"></div>
                        <div class="absolute -bottom-16 right-8 h-36 w-36 rounded-full bg-[#F7FFD9]/80 blur-2xl"></div>
                    </div>

                    <span class="chip chip-sky">
                        <x-nav-icon name="table" class="h-3 w-3" />
                        Laporan lengkap
                    </span>
                    <h2 class="mt-3 text-balance text-[21px] font-extrabold leading-tight tracking-[-0.03em]">
                        Rekap Nilai Keseluruhan
                    </h2>
                    <p class="mt-2 max-w-xl text-[12px] leading-relaxed text-slate-500">
                        Satu file berisi seluruh mahasiswa dengan nilai setiap pertemuan, persentase kehadiran,
                        rata-rata nilai, dan status kelulusan. Cocok untuk laporan akhir semester.
                    </p>

                    <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                        <li class="flex items-start gap-2 rounded-xl border border-black/5 bg-[#F9F9F6] px-3 py-2">
                            <span
                                class="mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full bg-[#C8F24D] text-[#0B0B0C]">
                                <x-nav-icon name="check" class="h-2.5 w-2.5" />
                            </span>
                            <span class="text-[11px] font-semibold leading-snug">Nilai tiap pertemuan</span>
                        </li>
                        <li class="flex items-start gap-2 rounded-xl border border-black/5 bg-[#F9F9F6] px-3 py-2">
                            <span
                                class="mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full bg-[#C8F24D] text-[#0B0B0C]">
                                <x-nav-icon name="check" class="h-2.5 w-2.5" />
                            </span>
                            <span class="text-[11px] font-semibold leading-snug">Persentase kehadiran</span>
                        </li>
                        <li class="flex items-start gap-2 rounded-xl border border-black/5 bg-[#F9F9F6] px-3 py-2">
                            <span
                                class="mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full bg-[#C8F24D] text-[#0B0B0C]">
                                <x-nav-icon name="check" class="h-2.5 w-2.5" />
                            </span>
                            <span class="text-[11px] font-semibold leading-snug">Rata-rata nilai</span>
                        </li>
                        <li class="flex items-start gap-2 rounded-xl border border-black/5 bg-[#F9F9F6] px-3 py-2">
                            <span
                                class="mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full bg-[#C8F24D] text-[#0B0B0C]">
                                <x-nav-icon name="check" class="h-2.5 w-2.5" />
                            </span>
                            <span class="text-[11px] font-semibold leading-snug">Status kelulusan</span>
                        </li>
                    </ul>

                    <p class="mt-4 text-[10.5px] leading-relaxed text-slate-500">
                        Satu baris per mahasiswa, satu kolom per pertemuan. Pilih kelas di samping bila hanya
                        membutuhkan sebagian data.
                    </p>
                </div>

                <div class="border-t border-black/5 bg-[#F9F9F6] p-5 sm:p-6 lg:border-l lg:border-t-0">
                    <form id="overall-export-form" action="{{ route('admin.export.overall') }}" method="GET"
                        class="flex h-full flex-col">
                        <div class="flex items-center justify-between gap-2">
                            <label class="field-label" for="overall-classroom">Cakupan kelas</label>
                            <span class="chip chip-sky">.XLSX</span>
                        </div>
                        <select id="overall-classroom" name="classroom_id" class="field-input">
                            <option value="">Semua kelas · {{ $stats['students'] }} mahasiswa</option>
                            @foreach ($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">
                                    {{ $classroom->name }} — {{ $classroom->course_name }}
                                    ({{ $classroom->students_count }} mahasiswa)
                                </option>
                            @endforeach
                        </select>
                        <p class="field-help">
                            Biarkan “Semua kelas” untuk rekap gabungan, atau pilih satu kelas agar file lebih ringkas.
                        </p>

                        <div class="mt-4 rounded-xl border border-black/5 bg-white px-3 py-2.5">
                            <p class="text-[8.5px] font-extrabold uppercase tracking-[0.14em] text-slate-500">
                                Kolom dalam file
                            </p>
                            <p class="mt-1 text-[10.5px] font-semibold leading-relaxed text-slate-600">
                                NIM · Nama · Kelas · Nilai pertemuan · Kehadiran · Rata-rata · Status kelulusan
                            </p>
                        </div>

                        <div class="mt-auto pt-5">
                            <button type="submit" class="btn btn-sky w-full">
                                <x-nav-icon name="download" class="h-3.5 w-3.5" />
                                Unduh Rekap .XLSX
                            </button>
                            <p class="mt-2 text-center text-[10px] font-semibold text-slate-500">
                                File langsung diunduh · dapat dibuka di Excel & Google Sheets
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        @php
            $sessionsPerClass = $sessions->countBy('classroom_id');
        @endphp

        <section id="export-pertemuan" class="space-y-3">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <p class="inline-flex items-center gap-2 text-[9px] font-extrabold uppercase tracking-[0.2em] text-[#0A66C8]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        Export per pertemuan
                    </p>
                    <h2 class="mt-1.5 text-[20px] font-extrabold leading-tight tracking-[-0.03em]">
                        Nilai per Kelas & Pertemuan
                    </h2>
                    <p class="mt-1 max-w-2xl text-[12px] leading-relaxed text-slate-500">
                        Berisi status kehadiran, nilai pre-test, tugas, nilai tambahan, total nilai, dan catatan asisten
                        untuk satu pertemuan.
                    </p>
                </div>
                <span class="chip self-start sm:self-auto">
                    {{ $sessions->count() }} file tersedia
                </span>
            </div>

            @if ($sessions->isNotEmpty())
                <section class="card px-4 py-4 sm:px-5">
                    <div class="flex flex-col gap-2.5 lg:flex-row lg:items-center">
                        <label class="relative flex-1 lg:max-w-md">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <x-nav-icon name="search" class="h-3.5 w-3.5" />
                            </span>
                            <input id="session-export-search" type="search"
                                placeholder="Cari kelas, mata kuliah, atau topik pertemuan…" class="field-input mt-0 pl-9"
                                autocomplete="off" aria-label="Cari pertemuan">
                        </label>

                        <label class="lg:w-64">
                            <select id="session-export-class" class="field-input mt-0" aria-label="Filter kelas">
                                <option value="">Semua kelas</option>
                                @foreach ($classrooms as $classroom)
                                    @if (($sessionsPerClass[$classroom->id] ?? 0) > 0)
                                        <option value="{{ $classroom->id }}">
                                            {{ $classroom->name }} ({{ $sessionsPerClass[$classroom->id] }} pertemuan)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </label>

                        <p class="whitespace-nowrap text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500 lg:ml-auto"
                            aria-live="polite">
                            <span id="session-export-count">{{ $sessions->count() }}</span> dari
                            {{ $sessions->count() }} pertemuan
                        </p>
                    </div>
                </section>
            @endif

            <div id="session-export-list" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($sessions as $session)
                    @php
                        $statusLabel = match ($session->status) {
                            'active' => 'Aktif',
                            'finished' => 'Selesai',
                            default => 'Draf',
                        };
                        $statusClass = match ($session->status) {
                            'active' => 'chip-lime',
                            'finished' => 'chip-emerald',
                            default => '',
                        };
                        $studentTotal = (int) ($session->classroom->students_count ?? 0);
                        $gradedPercent =
                            $studentTotal > 0
                                ? min(100, (int) round(($session->grades_count / $studentTotal) * 100))
                                : 0;
                        $search = mb_strtolower(
                            implode(
                                ' ',
                                array_filter([
                                    $session->classroom?->code,
                                    $session->classroom?->name,
                                    $session->classroom?->course_name,
                                    $session->title,
                                    'pertemuan ' . $session->meeting_number,
                                ]),
                            ),
                        );
                    @endphp

                    <article data-session-export-card data-classroom-id="{{ $session->classroom_id }}"
                        data-search="{{ $search }}"
                        class="card flex flex-col p-4 transition hover:border-black/10 hover:shadow-[0_18px_45px_-38px_rgba(11,11,12,.45)] sm:p-5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex min-w-0 items-center gap-2.5">
                                <span
                                    class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#0B0B0C] text-[11px] font-extrabold text-[#C8F24D]">
                                    P{{ $session->meeting_number }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="truncate text-[14px] font-bold leading-tight">
                                        {{ $session->classroom?->name ?? 'Kelas' }}
                                    </h3>
                                    <p class="mt-0.5 truncate text-[10px] text-slate-500">
                                        {{ $session->classroom?->course_name }}
                                    </p>
                                </div>
                            </div>
                            <span class="chip {{ $statusClass }} shrink-0">{{ $statusLabel }}</span>
                        </div>

                        <p class="mt-3 line-clamp-2 text-[12px] font-semibold leading-snug" title="{{ $session->title }}">
                            {{ $session->title }}
                        </p>
                        <p class="mt-1.5 inline-flex items-center gap-1.5 text-[10.5px] font-semibold text-slate-500">
                            <x-nav-icon name="calendar" class="h-3 w-3" />
                            {{ $session->session_date?->locale('id')->translatedFormat('d M Y') ?? 'Tanggal belum diatur' }}
                        </p>

                        <div class="mt-3 grid grid-cols-3 gap-1.5">
                            <div class="rounded-xl border border-black/5 bg-[#F9F9F6] px-2 py-2 text-center">
                                <p class="text-[14px] font-extrabold leading-none">{{ $studentTotal }}</p>
                                <p class="mt-1 text-[7.5px] font-extrabold uppercase tracking-[0.1em] text-slate-500">
                                    Mahasiswa</p>
                            </div>
                            <div class="rounded-xl border border-black/5 bg-[#F9F9F6] px-2 py-2 text-center">
                                <p class="text-[14px] font-extrabold leading-none">{{ $session->present_count }}</p>
                                <p class="mt-1 text-[7.5px] font-extrabold uppercase tracking-[0.1em] text-slate-500">
                                    Hadir</p>
                            </div>
                            <div class="rounded-xl border border-black/5 bg-[#F9F9F6] px-2 py-2 text-center">
                                <p class="text-[14px] font-extrabold leading-none">{{ $session->grades_count }}</p>
                                <p class="mt-1 text-[7.5px] font-extrabold uppercase tracking-[0.1em] text-slate-500">
                                    Nilai</p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="flex items-center justify-between gap-2 text-[9px] font-bold uppercase tracking-[0.1em] text-slate-500">
                                <span>Kelengkapan nilai</span>
                                <span>{{ $gradedPercent }}%</span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-black/5" role="progressbar"
                                aria-valuenow="{{ $gradedPercent }}" aria-valuemin="0" aria-valuemax="100"
                                aria-label="Kelengkapan nilai {{ $session->classroom?->name }} pertemuan {{ $session->meeting_number }}">
                                <span
                                    class="block h-full rounded-full {{ $gradedPercent >= 100 ? 'bg-emerald-500' : 'bg-[#0A66C8]' }}"
                                    style="width: {{ $gradedPercent }}%"></span>
                            </div>
                        </div>

                        <div class="mt-auto pt-4">
                            <a href="{{ route('admin.export.session', $session) }}"
                                class="btn btn-light w-full hover:border-[#0A66C8]/20 hover:bg-[#EAF4FF] hover:text-[#0A66C8]"
                                data-export-download
                                title="Unduh nilai {{ $session->classroom?->name }} pertemuan {{ $session->meeting_number }}">
                                <x-nav-icon name="download" class="h-3.5 w-3.5" />
                                Unduh Excel
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="card px-5 py-12 text-center md:col-span-2 xl:col-span-3">
                        <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
                            <x-nav-icon name="calendar" class="h-5 w-5" />
                        </span>
                        <p class="mt-3 text-[14px] font-bold">Belum ada pertemuan</p>
                        <p class="mt-1 text-[12px] text-slate-500">
                            Buat sesi praktikum terlebih dahulu agar nilainya bisa diunduh.
                        </p>
                        <a href="{{ route('sessions.index') }}" class="btn btn-light mt-4">
                            <x-nav-icon name="arrow-right" class="h-3.5 w-3.5" />
                            Buka Daftar Sesi
                        </a>
                    </div>
                @endforelse
            </div>

            <div id="session-export-empty" class="card hidden px-5 py-12 text-center">
                <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
                    <x-nav-icon name="search" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-[14px] font-bold">Tidak ada pertemuan yang cocok</p>
                <p class="mt-1 text-[12px] text-slate-500">
                    Coba kata kunci lain atau pilih kelas yang berbeda.
                </p>
                <button type="button" id="session-export-reset" class="btn btn-light mt-4">
                    <x-nav-icon name="refresh" class="h-3.5 w-3.5" />
                    Bersihkan Filter
                </button>
            </div>
        </section>

        <section class="card-soft flex flex-col gap-3 p-4 sm:flex-row sm:items-start sm:gap-3.5 sm:p-5">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#EAF4FF] text-[#0A66C8]">
                <x-nav-icon name="doc" class="h-4 w-4" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[12.5px] font-bold">File mengikuti data terbaru saat diunduh</p>
                <p class="mt-1 text-[11.5px] leading-relaxed text-slate-500">
                    Pastikan penilaian pertemuan sudah lengkap sebelum membuat laporan. File .xlsx dapat dibuka di
                    Excel, Google Sheets, atau LibreOffice Calc tanpa pengaturan tambahan.
                </p>
            </div>
            <span class="chip chip-lime shrink-0 self-start sm:ml-auto">Format .XLSX</span>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const $search = $('#session-export-search');
                const $classroom = $('#session-export-class');
                const $count = $('#session-export-count');
                const $empty = $('#session-export-empty');
                const $list = $('#session-export-list');

                const applyFilters = function() {
                    const query = ($search.val() || '').toString().trim().toLowerCase();
                    const classroomId = ($classroom.val() || '').toString();
                    let visible = 0;

                    $list.find('[data-session-export-card]').each(function() {
                        const card = $(this);
                        const matchesQuery = !query || (card.data('search') || '').includes(query);
                        const matchesClass = !classroomId || card.attr('data-classroom-id') === classroomId;
                        const show = matchesQuery && matchesClass;

                        card.toggle(show);
                        if (show) visible++;
                    });

                    $count.text(visible);

                    if ($list.find('[data-session-export-card]').length) {
                        $empty.toggleClass('hidden', visible !== 0);
                        $list.toggleClass('hidden', visible === 0);
                    }
                };

                $search.on('input', applyFilters);
                $classroom.on('change', applyFilters);

                $('#session-export-reset').on('click', function() {
                    $search.val('');
                    $classroom.val('');
                    applyFilters();
                    $search.trigger('focus');
                });

                $('#overall-export-form').on('submit', function() {
                    Sipro.toast('Menyiapkan rekap nilai. Unduhan akan segera dimulai.', 'info');
                });

                $(document).on('click', '[data-export-download]', function() {
                    Sipro.toast('Menyiapkan file Excel untuk pertemuan ini…', 'info');
                });
            });
        </script>
    @endpush
</x-app-layout>
