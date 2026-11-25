<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Pemeriksaan Tugas" :title="$assignment->title"
            :description="$assignment->description ?: 'Periksa pengumpulan PDF, unduh berkas jawaban, dan isi nilai tugas mahasiswa langsung dari halaman ini.'">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $assignment->session?->classroom?->name ?? 'Kelas' }}</span>
                <span class="chip">Pertemuan {{ $assignment->session?->meeting_number ?? '—' }}</span>
                <span class="chip">Tenggat
                    {{ $assignment->deadline?->locale('id')->translatedFormat('d M Y · H:i') ?? '—' }}</span>
                <span class="chip {{ $assignment->deadline?->isFuture() ? 'chip-lime' : 'chip-rose' }}">
                    {{ $assignment->deadline?->isFuture() ? 'Masih dibuka' : 'Ditutup' }}
                </span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('admin.submissions.index') }}" class="btn btn-light">
                    <x-nav-icon name="arrow-left" class="h-3.5 w-3.5" />
                    Daftar Tugas
                </a>
                @if ($assignment->file_path)
                    <a href="{{ route('assignments.file', $assignment) }}"
                        target="_blank" rel="noopener" class="btn btn-primary">
                        <x-nav-icon name="download" class="h-3.5 w-3.5" />
                        File Soal
                    </a>
                @endif
            </x-slot:actions>
        </x-page-header>

        <div id="submission-summary">
            @include('admin.submissions.partials.summary', [
                'assignment' => $assignment,
                'stats' => $stats,
            ])
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-1 flex-col gap-2 sm:flex-row">
                    <label class="relative flex-1">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-nav-icon name="search" class="h-3.5 w-3.5" />
                        </span>
                        <input id="submission-search" type="search" placeholder="Cari nama atau NIM mahasiswa…"
                            class="field-input mt-0 pl-9" autocomplete="off">
                    </label>
                    <select id="submission-status" class="field-input mt-0 sm:w-56">
                        <option value="all">Semua mahasiswa</option>
                        <option value="submitted">Sudah mengumpulkan</option>
                        <option value="not_submitted">Belum mengumpulkan</option>
                    </select>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span id="submission-count">{{ $students->count() }}</span> mahasiswa ditampilkan
                </p>
            </div>
        </section>

        <div id="submission-list" class="space-y-2.5">
            @forelse ($students as $mhs)
                @php
                    $hasSubmission = $mhs->submissions->isNotEmpty();
                    $search = mb_strtolower($mhs->name . ' ' . $mhs->nim_nip);
                @endphp

                <div id="submission-row-{{ $mhs->id }}" data-submission-row data-search="{{ $search }}"
                    data-submitted="{{ $hasSubmission ? '1' : '0' }}"
                    class="card overflow-hidden transition hover:border-black/10">
                    @include('admin.submissions.partials.student-row', [
                        'assignment' => $assignment,
                        'mhs' => $mhs,
                    ])
                </div>
            @empty
                <div class="card px-5 py-12 text-center">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
                        <x-nav-icon name="users" class="h-5 w-5" />
                    </span>
                    <p class="mt-3 text-[14px] font-bold">Belum ada mahasiswa di kelas ini</p>
                    <p class="mt-1 text-[12px] text-slate-500">Tambahkan mahasiswa melalui menu Data Mahasiswa.</p>
                </div>
            @endforelse
        </div>

        @if ($students->isNotEmpty())
            <div id="submission-empty" class="card hidden px-5 py-12 text-center">
                <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#F5F5F2] text-slate-500">
                    <x-nav-icon name="search" class="h-5 w-5" />
                </span>
                <p class="mt-3 text-[14px] font-bold">Tidak ada mahasiswa yang cocok</p>
                <p class="mt-1 text-[12px] text-slate-500">Ubah kata kunci pencarian atau pilihan status pengumpulan.</p>
            </div>
        @endif
    </div>

    <x-modal name="pdf-preview" maxWidth="4xl" title="Pratinjau PDF Pengumpulan"
        description="Berkas ditampilkan langsung dari penyimpanan. Gunakan tombol download jika pratinjau tidak muncul.">
        <div class="space-y-3">
            <div class="flex flex-col gap-2 rounded-2xl border border-black/5 bg-[#F9F9F6] px-3.5 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <p id="preview-name" class="truncate text-[12px] font-bold">—</p>
                    <p class="mt-0.5 text-[10px] text-slate-500">Pratinjau jawaban PDF mahasiswa</p>
                </div>
                <a id="preview-download" href="#" class="btn btn-primary btn-sm shrink-0">
                    <x-nav-icon name="download" class="h-3.5 w-3.5" />
                    Download PDF
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl border border-black/5 bg-[#F5F5F2]">
                <iframe id="preview-frame" src="about:blank" title="Pratinjau PDF pengumpulan mahasiswa"
                    class="h-[62vh] w-full"></iframe>
            </div>

            <p class="text-[10.5px] leading-relaxed text-slate-500">
                Jika browser memblokir pratinjau, buka PDF melalui tombol download lalu lihat dari perangkat Anda.
            </p>
        </div>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterRows = function() {
                    const query = ($('#submission-search').val() || '').toString().trim().toLowerCase();
                    const status = $('#submission-status').val() || 'all';
                    let visible = 0;

                    $('#submission-list [data-submission-row]').each(function() {
                        const row = $(this);
                        const matchesQuery = !query || (row.data('search') || '').includes(query);
                        const matchesStatus = status === 'all' ||
                            (status === 'submitted' && row.data('submitted') === 1) ||
                            (status === 'not_submitted' && row.data('submitted') === 0);
                        const show = matchesQuery && matchesStatus;

                        row.toggle(show);
                        if (show) visible++;
                    });

                    $('#submission-count').text(visible);
                    $('#submission-empty').toggle(visible === 0);
                };

                $('#submission-search').on('input', filterRows);
                $('#submission-status').on('change', filterRows);

                $(document).on('click', '[data-preview]', function() {
                    const button = $(this);

                    $('#preview-name').text(button.data('name') || 'PDF pengumpulan');
                    $('#preview-frame').attr('src', button.data('url'));
                    $('#preview-download').attr('href', button.data('download'));

                    Sipro.openModal('pdf-preview');
                });

                window.addEventListener('close-modal', function(event) {
                    if (event.detail !== 'pdf-preview') return;

                    window.setTimeout(function() {
                        $('#preview-frame').attr('src', 'about:blank');
                        $('#preview-name').text('—');
                        $('#preview-download').attr('href', '#');
                    }, 200);
                });
            });
        </script>
    @endpush
</x-app-layout>
