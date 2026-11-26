@forelse ($assignments as $a)
    @php
        $isOpen = $a->deadline && $a->deadline->isFuture();
        $fill = [
            'practicum_session_id' => $a->practicum_session_id,
            'title' => $a->title,
            'description' => $a->description,
            'deadline' => $a->deadline?->format('Y-m-d\TH:i'),
            '__file' => $a->file_path ? basename($a->file_path) : 'Belum ada file',
        ];
        $search = mb_strtolower($a->title . ' ' . ($a->session?->classroom?->name ?? '') . ' ' . ($a->session?->title ?? ''));
    @endphp

    <article data-assignment-card data-open="{{ $isOpen ? '1' : '0' }}" data-search="{{ $search }}"
        class="card px-4 py-4 transition hover:border-black/10 hover:shadow-[0_18px_45px_-38px_rgba(11,11,12,.45)] sm:px-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="flex min-w-0 items-start gap-3.5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
                    <x-nav-icon name="doc" class="h-5 w-5" />
                </span>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="chip chip-sky">{{ $a->session?->classroom?->name ?? 'Kelas' }}</span>
                        <span class="chip">Pertemuan {{ $a->session?->meeting_number ?? '—' }}</span>
                        <span class="chip {{ $isOpen ? 'chip-lime' : 'chip-rose' }}">
                            {{ $isOpen ? 'Dibuka' : 'Ditutup' }}
                        </span>
                    </div>

                    <h3 class="mt-2 text-[15px] font-bold leading-snug tracking-[-0.01em]">{{ $a->title }}</h3>
                    @if ($a->description)
                        <p class="mt-1 line-clamp-2 text-[12px] leading-relaxed text-slate-500">{{ $a->description }}</p>
                    @endif

                    <div class="mt-2.5 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[10px] font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-1">
                            <x-nav-icon name="clock" class="h-3 w-3" />
                            {{ $a->deadline?->locale('id')->translatedFormat('d M Y · H:i') ?? 'Tanpa deadline' }}
                        </span>
                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span class="inline-flex items-center gap-1">
                            <x-nav-icon name="upload" class="h-3 w-3" />
                            {{ $a->submissions_count }} pengumpulan
                        </span>
                        @if ($a->file_path)
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span class="truncate font-mono text-[9.5px]">{{ basename($a->file_path) }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 md:max-w-[16rem] md:justify-end">
                @if ($a->file_path)
                    <a href="{{ route('assignments.file', $a) }}" target="_blank"
                        rel="noopener" class="btn btn-light btn-sm">
                        <x-nav-icon name="download" class="h-3.5 w-3.5" />
                        File
                    </a>
                @endif
                <a href="{{ route('admin.submissions.show', $a) }}" class="btn btn-primary btn-sm">
                    <x-nav-icon name="eye" class="h-3.5 w-3.5" />
                    Periksa
                </a>
                <button type="button" class="btn btn-light btn-sm" data-form-modal="assignment-modal"
                    data-action="{{ route('admin.assignments.update', $a) }}" data-method="PUT"
                    data-title="Edit Tugas Mingguan" data-submit="Simpan Perubahan"
                    data-modal-description="Kosongkan file jika ingin mempertahankan file soal yang sudah ada."
                    data-fill="{{ json_encode($fill) }}">
                    <x-nav-icon name="edit" class="h-3.5 w-3.5" />
                    Edit
                </button>
                <button type="button" class="icon-btn hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                    data-delete="{{ route('admin.assignments.destroy', $a) }}" data-title="Hapus tugas mingguan?"
                    data-message="File soal dan seluruh PDF pengumpulan mahasiswa ikut terhapus."
                    data-confirm="Ya, hapus tugas" aria-label="Hapus tugas {{ $a->title }}">
                    <x-nav-icon name="trash" class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    </article>
@empty
    <div class="card px-5 py-12 text-center">
        <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
            <x-nav-icon name="upload" class="h-5 w-5" />
        </span>
        <p class="mt-3 text-[14px] font-bold">Belum ada tugas mingguan</p>
        <p class="mt-1 text-[12px] text-slate-500">Unggah file soal agar mahasiswa dapat mengumpulkan PDF.</p>
    </div>
@endforelse

@if ($assignments->hasPages())
    <div class="rounded-2xl border border-black/5 bg-white px-3 py-2">{{ $assignments->links() }}</div>
@endif
