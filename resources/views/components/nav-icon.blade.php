@props(['name'])

<svg {{ $attributes->merge(['class' => 'h-[18px] w-[18px]']) }} viewBox="0 0 24 24" fill="none"
    stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('dashboard')
            <path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h3A1.5 1.5 0 0 1 10 5.5v3A1.5 1.5 0 0 1 8.5 10h-3A1.5 1.5 0 0 1 4 8.5v-3Z" />
            <path d="M14 5.5A1.5 1.5 0 0 1 15.5 4h3A1.5 1.5 0 0 1 20 5.5v3A1.5 1.5 0 0 1 18.5 10h-3A1.5 1.5 0 0 1 14 8.5v-3Z" />
            <path d="M4 15.5A1.5 1.5 0 0 1 5.5 14h3a1.5 1.5 0 0 1 1.5 1.5v3A1.5 1.5 0 0 1 8.5 20h-3A1.5 1.5 0 0 1 4 18.5v-3Z" />
            <path d="M14 15.5A1.5 1.5 0 0 1 15.5 14h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a1.5 1.5 0 0 1-1.5-1.5v-3Z" />
            @break
        @case('qr')
            <path d="M4 8V6a2 2 0 0 1 2-2h2M16 4h2a2 2 0 0 1 2 2v2M20 16v2a2 2 0 0 1-2 2h-2M8 20H6a2 2 0 0 1-2-2v-2" />
            <path d="M8 12h8M12 8v8" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 7.5V12l3 2" />
            @break
        @case('doc')
            <path d="M7 3.5h6.5L18 8v10.5a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2Z" />
            <path d="M13.5 3.5V8H18M8.5 12.5h7M8.5 16h5" />
            @break
        @case('chart')
            <path d="M4 20V10M10 20V4M16 20v-7M22 20H2" />
            @break
        @case('calendar')
            <rect x="3.5" y="5" width="17" height="15" rx="2.5" />
            <path d="M8 3v4M16 3v4M3.5 10h17M8 14h.01M12 14h.01M16 14h.01" />
            @break
        @case('upload')
            <path d="M12 16V4m0 0 4 4m-4-4L8 8" />
            <path d="M4 16v2.5A2.5 2.5 0 0 0 6.5 21h11a2.5 2.5 0 0 0 2.5-2.5V16" />
            @break
        @case('megaphone')
            <path d="M4 10v4a2 2 0 0 0 2 2h2l7 4V4L8 8H6a2 2 0 0 0-2 2Z" />
            <path d="M18.5 8.5a5 5 0 0 1 0 7" />
            @break
        @case('building')
            <path d="M4 21V6.5a2 2 0 0 1 1.2-1.8l6-2.6A2 2 0 0 1 14 3.9V21" />
            <path d="M14 9h4.5A1.5 1.5 0 0 1 20 10.5V21M2.5 21h19M7.5 8h3M7.5 12h3M7.5 16h3M17 13h.01M17 17h.01" />
            @break
        @case('users')
            <circle cx="9" cy="8" r="3.2" />
            <path d="M3 20c0-3.1 2.7-5.5 6-5.5s6 2.4 6 5.5" />
            <path d="M16 5.2a3.2 3.2 0 0 1 0 6M18 14.8c2 .7 3.4 2.4 3.4 4.6" />
            @break
        @case('badge')
            <circle cx="12" cy="9" r="4" />
            <path d="M9 12.5 8 21l4-2 4 2-1-8.5" />
            @break
        @case('table')
            <rect x="3.5" y="4.5" width="17" height="15" rx="2.5" />
            <path d="M3.5 9.5h17M3.5 14.5h17M9.5 9.5v10" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="3.4" />
            <path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6" />
            @break
        @case('portal')
            <path d="M14 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4" />
            <path d="M10 8l4 4-4 4M14 12H4" />
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break
        @case('edit')
            <path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z" />
            <path d="m13.5 6.5 3 3" />
            @break
        @case('trash')
            <path d="M4 7h16M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" />
            <path d="M6.5 7l.8 11.2A2 2 0 0 0 9.3 20h5.4a2 2 0 0 0 2-1.8L17.5 7" />
            <path d="M10 11v5M14 11v5" />
            @break
        @case('search')
            <circle cx="11" cy="11" r="6.5" />
            <path d="m16 16 4 4" />
            @break
        @case('refresh')
            <path d="M20 11a8 8 0 1 0-2.3 5.7" />
            <path d="M20 5v6h-6" />
            @break
        @case('check')
            <path d="m5 12.5 4.5 4.5L19 7" />
            @break
        @case('x')
            <path d="m7 7 10 10M17 7 7 17" />
            @break
        @case('download')
            <path d="M12 4v11m0 0 4-4m-4 4-4-4" />
            <path d="M5 18v1.5A1.5 1.5 0 0 0 6.5 21h11a1.5 1.5 0 0 0 1.5-1.5V18" />
            @break
        @case('eye')
            <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" />
            <circle cx="12" cy="12" r="3" />
            @break
        @case('arrow-left')
            <path d="M19 12H5m0 0 5-5m-5 5 5 5" />
            @break
        @case('arrow-right')
            <path d="M5 12h14m0 0-5-5m5 5-5 5" />
            @break
        @case('logout')
            <path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3" />
            <path d="M10 8l-4 4 4 4M6 12h9" />
            @break
        @case('sparkles')
            <path d="M12 3.5 13.6 8 18 9.6 13.6 11.2 12 15.7 10.4 11.2 6 9.6 10.4 8 12 3.5Z" />
            <path d="M18.5 15.5l.7 1.8 1.8.7-1.8.7-.7 1.8-.7-1.8-1.8-.7 1.8-.7.7-1.8Z" />
            @break
        @case('send')
            <path d="M21 3 10.5 13.5M21 3l-6.8 18-3.7-7.5L3 9.8 21 3Z" />
            @break
    @endswitch
</svg>
