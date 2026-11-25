@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-3 text-[11px] font-medium leading-relaxed text-emerald-700']) }}>
        {{ $status }}
    </div>
@endif
