<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/favicon-64.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/apple-touch-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SI-PROG | Sistem Informasi Pemrograman' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }

        @media (prefers-reduced-motion: reduce) {
            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }

            html {
                scroll-behavior: auto;
            }
        }
    </style>
    <noscript>
        <style>
            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
            }
        </style>
    </noscript>
</head>

<body class="bg-[#F5F5F2] font-brand text-[#0B0B0C] antialiased" x-data="{ sidebarOpen: false }"
    @keydown.escape.window="sidebarOpen = false">
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[60] focus:rounded-full focus:bg-[#0B0B0C] focus:px-4 focus:py-2 focus:text-xs focus:font-bold focus:text-white">
        Lewati ke konten
    </a>

    <div class="p-2 sm:p-3">
        <div class="mx-auto flex min-h-[calc(100svh-1rem)] w-full max-w-[1600px] gap-3">
            <!-- Sidebar Desktop & Mobile -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex min-w-0 flex-1 flex-col">
                <!-- Topbar -->
                <header
                    class="sticky top-2 z-30 flex items-center justify-between gap-3 rounded-[22px] border border-black/5 bg-white/85 px-3 py-2.5 shadow-[0_18px_45px_-38px_rgba(11,11,12,.45)] backdrop-blur-md sm:px-4">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <button @click="sidebarOpen = !sidebarOpen" aria-label="Buka menu"
                            class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-black/5 bg-[#F5F5F2] text-slate-600 transition hover:bg-[#EDEDE8] lg:hidden">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </button>

                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 lg:hidden">
                            <span class="grid h-9 w-9 place-items-center overflow-hidden rounded-xl bg-white shadow-sm">
                                <img src="{{ asset('assets/logo1x1.png') }}" alt="" width="32" height="32"
                                    class="h-8 w-8 object-contain" decoding="async">
                            </span>
                        </a>

                        <div class="hidden min-w-0 items-center gap-2.5 sm:flex">
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-[#C8F24D]"></span>
                            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">Ruang
                                Praktikum</span>
                            <span class="h-4 w-px bg-black/10" aria-hidden="true"></span>
                            <span class="truncate text-[12px] font-semibold text-[#0B0B0C]">
                                {{ auth()->user()->role->label() }}
                            </span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('profile.edit') }}"
                            class="hidden max-w-[18rem] items-center gap-2 rounded-full border border-black/5 bg-[#F5F5F2] py-1 pl-1 pr-3 transition hover:bg-[#EDEDE8] sm:flex">
                            <span
                                class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-[#0B0B0C] text-[10px] font-extrabold uppercase text-white">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </span>
                            <span class="min-w-0">
                                <span
                                    class="block max-w-[12rem] truncate text-[11px] font-semibold leading-tight">{{ auth()->user()->name }}</span>
                                <span
                                    class="block max-w-[12rem] truncate text-[9px] uppercase tracking-[0.12em] text-slate-500">{{ auth()->user()->nim_nip }}</span>
                            </span>
                        </a>
                        <button type="button" data-logout
                            class="group inline-flex items-center gap-2 rounded-full border border-black/10 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                            <x-nav-icon name="logout"
                                class="h-3.5 w-3.5 text-slate-400 transition group-hover:text-rose-500" />
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </div>
                </header>

                <!-- Server flash bridge — consumed by the Alpine toast store -->
                <div id="server-flash" hidden
                    @if (session('success')) data-success="{{ session('success') }}" @endif
                    @if (session('error')) data-error="{{ session('error') }}" @endif
                    @if ($errors->any()) data-error="{{ $errors->first() }}" @endif></div>

                <!-- Page Content -->
                <main id="main-content" class="flex-1 px-1 pb-6 pt-4 sm:px-2 sm:pt-5">
                    {{ $slot }}
                </main>

                <footer
                    class="flex flex-wrap items-center justify-between gap-2 px-2 pb-1 text-[9px] font-bold uppercase tracking-[0.16em] text-slate-500">
                    <span>SI-PROG · Sistem Informasi Praktikum</span>
                    <span>&copy; {{ date('Y') }}</span>
                </footer>
            </div>
        </div>
    </div>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    <x-toast-stack />
    <x-confirm-modal />

    @stack('scripts')
</body>

</html>
