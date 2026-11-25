@props([
    'label',
    'value',
    'note' => null,
    'icon' => 'chart',
    'tone' => 'sky',
])

@php
    $tones = [
        'sky' => 'bg-[#EAF4FF] text-[#0A66C8]',
        'lime' => 'bg-[#F7FFD9] text-[#3F5200]',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'indigo' => 'bg-[#F1EEFF] text-indigo-700',
        'amber' => 'bg-amber-50 text-amber-700',
        'rose' => 'bg-rose-50 text-rose-600',
        'ink' => 'bg-[#0B0B0C] text-[#C8F24D]',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'card p-4 sm:p-5']) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">{{ $label }}</p>
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl {{ $tones[$tone] ?? $tones['sky'] }}">
            <x-nav-icon :name="$icon" class="h-4 w-4" />
        </span>
    </div>
    <p class="mt-3 text-[2rem] font-extrabold leading-none tracking-[-0.04em]">{{ $value }}</p>
    @if ($note)
        <p class="mt-1.5 text-[11px] text-slate-500">{{ $note }}</p>
    @endif
</div>
