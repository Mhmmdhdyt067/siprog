@forelse ($sessions as $s)
    @php
        $isActive = $s->status === 'active';
        $search = mb_strtolower($s->title . ' ' . ($s->classroom?->name ?? '') . ' ' . $s->meeting_number);
        $fill = [
            'classroom_id' => $s->classroom_id,
            'meeting_number' => $s->meeting_number,
            'title' => $s->title,
            'description' => $s->description,
            'session_date' => $s->session_date?->format('Y-m-d'),
        ];
    @endphp

    <article data-session-card data-status="{{ $s->status }}" data-search="{{ $search }}"
        class="card px-4 py-4 transition hover:border-black/10 hover:shadow-[0_18px_45px_-38px_rgba(11,11,12,.45)] sm:px-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex min-w-0 items-start gap-3.5">
                <span
                    class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-[#0B0B0C] text-[13px] font-extrabold text-[#C8F24D]">
                    P{{ $s->meeting_number }}
                </span>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="chip {{ $isActive ? 'chip-lime' : 'chip-emerald' }}">
                            <span
                                class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-[#C8F24D]' : 'bg-emerald-500' }}"></span>
                            {{ $isActive ? 'Aktif' : 'Selesai' }}
                        </span>
                        <span class="chip chip-sky">{{ $s->classroom?->name ?? 'Kelas' }}</span>
                        <span class="chip">{{ $s->session_date?->locale('id')->translatedFormat('d M Y') }}</span>
                        @if ($isActive && $s->qr_expires_at)
                            <span class="chip chip-amber">QR s.d. {{ $s->qr_expires_at->format('H:i') }}</span>
                        @endif
                    </div>

                    <h3 class="mt-2 text-[15px] font-bold leading-snug tracking-[-0.01em]">{{ $s->title }}</h3>
                    <p class="mt-1 line-clamp-2 text-[12px] leading-relaxed text-slate-500">
                        {{ $s->description ?: 'Belum ada deskripsi materi untuk pertemuan ini.' }}
                    </p>

                    <div class="mt-2.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-[10px] font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-1">
                            <x-nav-icon name="users" class="h-3 w-3" />
                            {{ $s->attendances_count }} scan presensi
                        </span>
                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span class="inline-flex items-center gap-1">
                            <x-nav-icon name="chart" class="h-3 w-3" />
                            {{ $s->grades_count }} nilai terisi
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-1.5 lg:shrink-0 lg:justify-end">
                <a href="{{ route('sessions.qr', $s) }}" class="btn btn-primary btn-sm">
                    <x-nav-icon name="qr" class="h-3.5 w-3.5" />
                    QR
                </a>
                <a href="{{ route('grading.index', $s) }}" class="btn btn-light btn-sm">
                    <x-nav-icon name="chart" class="h-3.5 w-3.5" />
                    Nilai
                </a>
                @if (auth()->user()->role === \App\Enums\UserRole::ADMIN || auth()->user()->role === \App\Enums\UserRole::ASISTEN)
                    <a href="{{ route('admin.pretest.index', $s) }}" class="btn btn-light btn-sm">
                        <x-nav-icon name="doc" class="h-3.5 w-3.5" />
                        Pre-Test
                    </a>
                @endif
                <button type="button" class="icon-btn" data-form-modal="session-modal"
                    data-action="{{ route('sessions.update', $s) }}" data-method="PUT"
                    data-title="Edit Sesi Pertemuan" data-submit="Simpan Perubahan"
                    data-modal-description="Perbarui detail pertemuan tanpa mengubah token QR yang sedang tampil."
                    data-fill="{{ json_encode($fill) }}" aria-label="Edit sesi {{ $s->title }}">
                    <x-nav-icon name="edit" class="h-3.5 w-3.5" />
                </button>
                <button type="button" class="icon-btn hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                    data-delete="{{ route('sessions.destroy', $s) }}" data-title="Hapus sesi pertemuan?"
                    data-message="Presensi, nilai, soal pre-test, tugas, dan feedback pada pertemuan ini ikut terhapus."
                    data-confirm="Ya, hapus sesi" aria-label="Hapus sesi {{ $s->title }}">
                    <x-nav-icon name="trash" class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    </article>
@empty
    <div class="card px-5 py-12 text-center">
        <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
            <x-nav-icon name="calendar" class="h-5 w-5" />
        </span>
        <p class="mt-3 text-[14px] font-bold">Belum ada sesi praktikum</p>
        <p class="mt-1 text-[12px] text-slate-500">Buat sesi baru untuk mengaktifkan QR presensi di proyektor.</p>
    </div>
@endforelse

@if ($sessions->hasPages())
    <div class="rounded-2xl border border-black/5 bg-white px-3 py-2">{{ $sessions->links() }}</div>
@endif
