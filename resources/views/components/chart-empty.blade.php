@props([
    'icon' => 'chart',
    'title',
    'description' => null,
])

<div
    {{ $attributes->merge(['class' => 'flex h-full flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-black/10 bg-[#F9F9F6] px-5 text-center']) }}>
    <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#EAF4FF] text-[#0A66C8]">
        <x-nav-icon :name="$icon" class="h-4 w-4" />
    </span>
    <p class="text-[12px] font-bold">{{ $title }}</p>
    @if ($description)
        <p class="max-w-[18rem] text-[10.5px] leading-relaxed text-slate-500">{{ $description }}</p>
    @endif
</div>
