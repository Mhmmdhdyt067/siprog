<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#C8F24D] px-6 py-3 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#0B0B0C] transition hover:bg-[#DAFF73] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0A66C8]/40 focus-visible:ring-offset-2 disabled:opacity-60']) }}>
    {{ $slot }}
</button>
