@php
    $percentage = (float) ($stats['percentage'] ?? 0);
    $average = $stats['average'];
@endphp

<div class="card overflow-hidden">
    <div class="flex flex-col gap-4 px-4 py-4 sm:px-5 lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0">
            <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Progres Pengumpulan</p>
            <p class="mt-1.5 text-[2.1rem] font-extrabold leading-none tracking-[-0.04em]">{{ $percentage }}<span
                    class="text-base font-bold text-slate-400">%</span></p>
            <p class="mt-1.5 text-[11.5px] leading-relaxed text-slate-500">
                <span class="font-bold text-[#0B0B0C]">{{ $stats['submitted'] }}</span> dari
                <span class="font-bold text-[#0B0B0C]">{{ $stats['total'] }}</span> mahasiswa sudah mengumpulkan PDF.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:w-auto">
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 px-3 py-2.5">
                <p class="text-[9px] font-extrabold uppercase tracking-[0.14em] text-emerald-700">Terkumpul</p>
                <p class="mt-1 font-mono text-[18px] font-extrabold leading-none text-emerald-800">
                    {{ $stats['submitted'] }}</p>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-rose-50/70 px-3 py-2.5">
                <p class="text-[9px] font-extrabold uppercase tracking-[0.14em] text-rose-600">Belum</p>
                <p class="mt-1 font-mono text-[18px] font-extrabold leading-none text-rose-700">
                    {{ $stats['not_submitted'] }}</p>
            </div>
            <div class="rounded-2xl border border-sky-100 bg-[#EAF4FF] px-3 py-2.5">
                <p class="text-[9px] font-extrabold uppercase tracking-[0.14em] text-[#0A66C8]">Dinilai</p>
                <p class="mt-1 font-mono text-[18px] font-extrabold leading-none text-[#0A66C8]">{{ $stats['graded'] }}</p>
            </div>
            <div class="rounded-2xl border border-lime-200 bg-[#F7FFD9] px-3 py-2.5">
                <p class="text-[9px] font-extrabold uppercase tracking-[0.14em] text-[#3F5200]">Rata-rata</p>
                <p class="mt-1 font-mono text-[18px] font-extrabold leading-none text-[#3F5200]">
                    {{ $average !== null ? number_format((float) $average, 2) : '—' }}</p>
            </div>
        </div>
    </div>

    <div class="h-2 w-full bg-[#F5F5F2]" role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0"
        aria-valuemax="100" aria-label="Progres pengumpulan tugas">
        <div class="h-full rounded-r-full {{ $percentage >= 100 ? 'bg-[#C8F24D]' : 'bg-[#0A66C8]' }}"
            style="width: {{ $percentage }}%"></div>
    </div>
</div>
