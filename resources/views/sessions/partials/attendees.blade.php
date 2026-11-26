@php
    $statusStyles = [
        'hadir' => 'chip-emerald',
        'izin' => 'chip-sky',
        'sakit' => 'chip-amber',
        'alpha' => 'chip-rose',
    ];
@endphp

<div class="space-y-2">
    @forelse ($attendees as $attendance)
        <div class="flex items-center gap-3 rounded-xl border border-black/5 bg-[#F9F9F6] px-3 py-2.5">
            <span
                class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#0B0B0C] text-[11px] font-extrabold uppercase text-white">
                {{ mb_substr($attendance->user?->name ?? '?', 0, 1) }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-[12.5px] font-bold leading-tight">{{ $attendance->user?->name ?? 'Mahasiswa' }}
                </p>
                <p class="mt-0.5 truncate text-[10px] text-slate-500">
                    {{ $attendance->user?->nim_nip ?? '—' }}
                    @if ($attendance->scanned_at)
                        · {{ $attendance->scanned_at->locale('id')->translatedFormat('H:i:s') }}
                    @endif
                </p>
            </div>
            <span class="chip shrink-0 {{ $statusStyles[$attendance->status] ?? '' }}">
                {{ ucfirst($attendance->status) }}
            </span>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-black/10 px-4 py-10 text-center">
            <span class="mx-auto grid h-11 w-11 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
                <x-nav-icon name="users" class="h-5 w-5" />
            </span>
            <p class="mt-3 text-[13px] font-bold">Belum ada yang scan</p>
            <p class="mt-1 text-[11px] leading-relaxed text-slate-500">Kehadiran mahasiswa akan muncul otomatis di
                panel ini.</p>
        </div>
    @endforelse
</div>
