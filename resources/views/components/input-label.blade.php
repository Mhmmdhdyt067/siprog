@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500']) }}>
    {{ $value ?? $slot }}
</label>
