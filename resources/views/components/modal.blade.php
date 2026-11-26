@props([
    'name',
    'show' => false,
    'maxWidth' => 'lg',
    'title' => null,
    'description' => null,
])

@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
    ][$maxWidth] ?? 'sm:max-w-lg';
@endphp

<div x-data="{ show: @js($show) }" x-cloak x-show="show"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    x-effect="document.body.style.overflow = show ? 'hidden' : ''"
    data-modal-name="{{ $name }}" class="fixed inset-0 z-[80] flex items-end justify-center p-3 sm:items-center sm:p-6"
    role="dialog" aria-modal="true" @if ($title) aria-label="{{ $title }}" @endif>

    <div x-show="show" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="absolute inset-0 bg-[#0B0B0C]/45 backdrop-blur-sm"
        @click="show = false"></div>

    <div x-show="show" x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="opacity-0 translate-y-5 sm:translate-y-3 sm:scale-[.98]"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 sm:scale-[.98]"
        class="relative flex max-h-[calc(100svh-1.5rem)] w-full flex-col overflow-hidden rounded-[26px] border border-black/5 bg-white shadow-[0_40px_120px_-40px_rgba(11,11,12,.55)] {{ $maxWidth }}">

        @if ($title)
            <div class="flex items-start justify-between gap-4 border-b border-black/5 px-5 py-4 sm:px-6">
                <div class="min-w-0">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-[#0A66C8]">SI-PROG</p>
                    <h2 data-modal-title class="mt-1 text-[17px] font-bold leading-tight tracking-[-0.02em]">
                        {{ $title }}</h2>
                    @if ($description)
                        <p data-modal-description class="mt-1 text-[11.5px] leading-relaxed text-slate-500">
                            {{ $description }}</p>
                    @endif
                </div>
                <button type="button" data-modal-close aria-label="Tutup modal"
                    class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-black/5 text-slate-500 transition hover:bg-[#F5F5F2] hover:text-[#0B0B0C]">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="m7 7 10 10M17 7 7 17" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5 sm:px-6">
            {{ $slot }}
        </div>

        @if (isset($footer))
            <div class="border-t border-black/5 bg-[#FBFBF9] px-5 py-4 sm:px-6">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
