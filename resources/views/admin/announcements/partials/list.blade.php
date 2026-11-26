@php
    $typeStyles = [
        'info' => ['chip' => 'chip-sky', 'icon' => 'megaphone', 'bg' => 'bg-[#EAF4FF] text-[#0A66C8]'],
        'jadwal' => ['chip' => 'chip-indigo', 'icon' => 'calendar', 'bg' => 'bg-[#F1EEFF] text-indigo-700'],
        'tugas' => ['chip' => 'chip-amber', 'icon' => 'upload', 'bg' => 'bg-amber-50 text-amber-700'],
    ];
@endphp

@forelse ($announcements as $info)
    @php
        $style = $typeStyles[$info->type] ?? $typeStyles['info'];
        $fill = [
            'title' => $info->title,
            'content' => $info->content,
            'type' => $info->type,
            'classroom_id' => $info->classroom_id,
        ];
        $search = mb_strtolower($info->title . ' ' . $info->content . ' ' . ($info->classroom?->name ?? ''));
    @endphp

    <article data-announcement-card data-type="{{ $info->type }}" data-search="{{ $search }}"
        class="card px-4 py-4 transition hover:border-black/10 hover:shadow-[0_18px_45px_-38px_rgba(11,11,12,.45)] sm:px-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl {{ $style['bg'] }}">
                <x-nav-icon :name="$style['icon']" class="h-4 w-4" />
            </span>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="chip {{ $style['chip'] }}">{{ ucfirst($info->type) }}</span>
                    <span class="chip">{{ $info->classroom?->name ?? 'Semua Kelas' }}</span>
                    <span
                        class="text-[10px] text-slate-500">{{ $info->created_at->locale('id')->diffForHumans() }}</span>
                </div>

                <h3 class="mt-1.5 text-[14.5px] font-bold leading-snug tracking-[-0.01em]">{{ $info->title }}</h3>
                <p class="mt-1 line-clamp-3 whitespace-pre-line text-[12px] leading-relaxed text-slate-500">
                    {{ $info->content }}</p>
            </div>

            <div class="flex shrink-0 items-center gap-1.5 sm:flex-col sm:items-end">
                <button type="button" class="icon-btn" data-form-modal="announcement-modal"
                    data-action="{{ route('admin.announcements.update', $info) }}" data-method="PUT"
                    data-title="Edit Pemberitahuan" data-submit="Simpan Perubahan"
                    data-modal-description="Perbarui isi, tipe, atau target kelas pemberitahuan ini."
                    data-fill="{{ json_encode($fill) }}" aria-label="Edit {{ $info->title }}">
                    <x-nav-icon name="edit" class="h-3.5 w-3.5" />
                </button>
                <button type="button" class="icon-btn hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                    data-delete="{{ route('admin.announcements.destroy', $info) }}"
                    data-title="Hapus pemberitahuan?" data-message="Pemberitahuan akan hilang dari dashboard mahasiswa."
                    data-confirm="Ya, hapus" aria-label="Hapus {{ $info->title }}">
                    <x-nav-icon name="trash" class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    </article>
@empty
    <div class="card px-5 py-12 text-center">
        <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
            <x-nav-icon name="megaphone" class="h-5 w-5" />
        </span>
        <p class="mt-3 text-[14px] font-bold">Belum ada pemberitahuan</p>
        <p class="mt-1 text-[12px] text-slate-500">Buat info tugas, jadwal, atau pengumuman untuk mahasiswa.</p>
    </div>
@endforelse

@if ($announcements->hasPages())
    <div class="rounded-2xl border border-black/5 bg-white px-3 py-2">{{ $announcements->links() }}</div>
@endif
