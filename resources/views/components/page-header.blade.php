@props([
    'eyebrow' => 'Ruang Praktikum',
    'title',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'relative isolate overflow-hidden rounded-[26px] border border-black/5 bg-white px-5 py-5 sm:px-6 sm:py-6']) }}>
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -right-14 -top-20 h-52 w-52 rounded-full bg-[#EAF4FF] blur-2xl"></div>
        <div class="absolute -bottom-24 right-24 h-40 w-40 rounded-full bg-[#F7FFD9] blur-2xl"></div>
        <div class="absolute right-10 top-8 h-16 w-16 rounded-full border border-[#0A66C8]/10"></div>
        <div class="absolute right-24 top-16 h-6 w-6 rounded-full border border-[#C8F24D]/70"></div>
    </div>

    <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="inline-flex items-center gap-2 text-[9px] font-extrabold uppercase tracking-[0.2em] text-[#0A66C8]">
                <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                {{ $eyebrow }}
            </p>
            <h1 class="mt-2 text-balance text-[clamp(1.35rem,2.5vw,1.9rem)] font-bold leading-[1.1] tracking-[-0.035em]">
                {{ $title }}
            </h1>
            @if ($description)
                <p class="mt-1.5 max-w-2xl text-[12.5px] leading-relaxed text-slate-500">{{ $description }}</p>
            @endif
            @if (isset($meta))
                <div class="mt-3 flex flex-wrap items-center gap-2">{{ $meta }}</div>
            @endif
        </div>

        @if (isset($actions))
            <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actions }}</div>
        @endif
    </div>
</section>
