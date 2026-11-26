@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-[14px] font-medium text-[#0B0B0C] shadow-none transition placeholder:font-normal placeholder:text-slate-400 focus:border-[#0A66C8] focus:ring-2 focus:ring-[#0A66C8]/20 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400']) }}>
