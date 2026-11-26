@forelse ($classrooms as $c)
    @php
        $fill = [
            'code' => $c->code,
            'name' => $c->name,
            'course_name' => $c->course_name,
            'schedule_day' => $c->schedule_day,
            'schedule_time' => $c->schedule_time,
            'room' => $c->room,
        ];
        $search = mb_strtolower($c->code . ' ' . $c->name . ' ' . $c->course_name . ' ' . $c->schedule_day . ' ' . $c->room);
    @endphp

    <article data-classroom-card data-search="{{ $search }}"
        class="card flex flex-col p-4 transition hover:border-black/10 hover:shadow-[0_18px_45px_-38px_rgba(11,11,12,.45)] sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <span class="chip chip-sky font-mono">{{ $c->code }}</span>
                <h3 class="mt-2 text-[17px] font-bold leading-tight tracking-[-0.02em]">{{ $c->name }}</h3>
                <p class="mt-1 text-[11.5px] leading-relaxed text-slate-500">{{ $c->course_name }}</p>
            </div>
            <div class="flex shrink-0 flex-col gap-1.5 sm:flex-row">
                <button type="button" class="icon-btn" data-form-modal="classroom-modal"
                    data-action="{{ route('admin.classrooms.update', $c) }}" data-method="PUT"
                    data-title="Edit Data Kelas" data-submit="Simpan Perubahan"
                    data-modal-description="Perbarui identitas, jadwal, atau ruang kelas praktikum."
                    data-fill="{{ json_encode($fill) }}" aria-label="Edit kelas {{ $c->name }}">
                    <x-nav-icon name="edit" class="h-3.5 w-3.5" />
                </button>
                <button type="button" class="icon-btn hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                    data-delete="{{ route('admin.classrooms.destroy', $c) }}" data-title="Hapus kelas praktikum?"
                    data-message="Seluruh sesi, presensi, tugas, dan nilai pada kelas ini ikut terhapus."
                    data-confirm="Ya, hapus kelas" aria-label="Hapus kelas {{ $c->name }}">
                    <x-nav-icon name="trash" class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-2">
            <div class="rounded-xl border border-black/5 bg-[#F9F9F6] px-3 py-2.5">
                <p class="text-[8.5px] font-extrabold uppercase tracking-[0.14em] text-slate-500">Jadwal</p>
                <p class="mt-1 text-[11.5px] font-semibold leading-snug">{{ $c->schedule_day }},
                    {{ $c->schedule_time }}</p>
            </div>
            <div class="rounded-xl border border-black/5 bg-[#F9F9F6] px-3 py-2.5">
                <p class="text-[8.5px] font-extrabold uppercase tracking-[0.14em] text-slate-500">Ruang</p>
                <p class="mt-1 text-[11.5px] font-semibold leading-snug">{{ $c->room }}</p>
            </div>
        </div>

        <div class="mt-auto flex flex-wrap items-center gap-x-3 gap-y-1 border-t border-black/5 pt-3 text-[10px] font-semibold text-slate-500">
            <span class="inline-flex items-center gap-1"><x-nav-icon name="users" class="h-3 w-3" />
                {{ $c->students_count }} mahasiswa</span>
            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
            <span class="inline-flex items-center gap-1"><x-nav-icon name="badge" class="h-3 w-3" />
                {{ $c->assistants_count }} asisten</span>
            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
            <span class="inline-flex items-center gap-1"><x-nav-icon name="calendar" class="h-3 w-3" />
                {{ $c->sessions_count }} sesi</span>
        </div>
    </article>
@empty
    <div class="card px-5 py-12 text-center sm:col-span-2 xl:col-span-3">
        <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
            <x-nav-icon name="building" class="h-5 w-5" />
        </span>
        <p class="mt-3 text-[14px] font-bold">Belum ada kelas</p>
        <p class="mt-1 text-[12px] text-slate-500">Tambahkan kelas praktikum untuk mulai membuat sesi.</p>
    </div>
@endforelse
