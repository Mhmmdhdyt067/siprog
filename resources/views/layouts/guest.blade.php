<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/favicon-64.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/apple-touch-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Masuk ke SI-PROG, sistem informasi praktikum pemrograman.">
    <title>{{ config('app.name', 'SI-PROG') }} · Sistem Informasi Praktikum</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none;
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

<body class="bg-[#F5F5F2] font-brand text-[#0B0B0C] antialiased">
    <div class="p-2 sm:p-3">
        <div
            class="grid min-h-[calc(100svh-1rem)] overflow-hidden rounded-[28px] bg-white sm:rounded-[36px] lg:grid-cols-[1.06fr_1fr]">
            <aside data-hero-3d
                class="relative isolate overflow-hidden bg-[linear-gradient(178deg,#0B6FD3_0%,#1B93EC_40%,#6ACBFA_76%,#B4E8FF_100%)] px-6 py-7 text-white sm:px-9 sm:py-9 lg:flex lg:flex-col lg:justify-between lg:p-10">
                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                    <div class="absolute -left-28 top-1/4 h-[24rem] w-[24rem] rounded-full bg-white/20 blur-3xl"></div>
                    <div class="absolute -right-20 top-1/3 h-[20rem] w-[20rem] rounded-full bg-white/25 blur-3xl"></div>
                    <div
                        class="absolute -bottom-32 left-1/2 h-[22rem] w-[120%] -translate-x-1/2 rounded-[50%] bg-white/40 blur-3xl">
                    </div>
                    <div class="absolute bottom-[22%] left-[8%] h-20 w-48 rounded-full bg-white/35 blur-2xl"></div>
                    <div class="absolute bottom-[30%] right-[10%] h-16 w-40 rounded-full bg-white/30 blur-2xl"></div>
                </div>

                <div data-aos="fade-down" data-aos-once="true"
                    class="relative z-10 flex items-center justify-between gap-4">
                    <a href="{{ route('portal') }}" class="flex items-center gap-2.5">
                        <span
                            class="grid h-10 w-10 place-items-center overflow-hidden rounded-full bg-white shadow-sm ring-1 ring-white/60">
                            <img src="{{ asset('assets/logo1x1.png') }}" alt="" width="32" height="32"
                                class="h-9 w-9 object-contain" decoding="async">
                        </span>
                        <span class="text-[16px] font-extrabold tracking-tight sm:text-[17px]">SI-PROG</span>
                    </a>
                    <a href="{{ route('portal') }}" aria-label="Kembali ke portal"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:hidden">
                        <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>
                    <a href="{{ route('portal') }}"
                        class="hidden items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-[9px] font-bold uppercase tracking-[0.16em] backdrop-blur transition hover:bg-white/20 sm:inline-flex">
                        Kembali ke portal
                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" aria-hidden="true">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>

                <div class="relative z-10 mt-8 max-w-md lg:flex lg:flex-1 lg:flex-col lg:justify-center">
                    <p data-aos="fade-up" data-aos-once="true"
                        class="hidden items-center gap-2 text-[9px] font-bold uppercase tracking-[0.2em] text-white/90 lg:flex">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        Sistem Informasi Praktikum
                    </p>
                    <h1 data-aos="fade-up" data-aos-delay="80" data-aos-once="true"
                        class="mt-3 text-balance text-[clamp(1.9rem,4.4vw,3.1rem)] font-semibold leading-[1.06] tracking-[-0.045em] lg:mt-5">
                        Masuk ke ruang praktikummu.
                    </h1>
                    <p data-aos="fade-up" data-aos-delay="150" data-aos-once="true"
                        class="mt-3 max-w-sm text-[12px] leading-relaxed text-white/90 sm:text-[13px]">
                        Jadwal, presensi QR, pre-test, tugas, dan nilai. Satu akun untuk seluruh alur praktikum.
                    </p>
                </div>

                <div data-aos="fade-up" data-aos-delay="220" data-aos-once="true"
                    class="relative z-10 hidden pt-10 lg:block">
                    <div class="hero-3d-stage relative flex h-[10.5rem] items-center justify-center gap-2 xl:gap-4"
                        aria-hidden="true">
                        <div class="hero-3d-inner flex h-full w-full items-center justify-center gap-2 xl:gap-4">
                        <div
                            class="hero-3d-card w-32 shrink-0 -rotate-6 rounded-2xl bg-white p-3 text-[#0B0B0C] shadow-[0_24px_60px_-24px_rgba(3,27,60,.55)] ring-1 ring-black/5 xl:w-36">
                            <div class="flex items-center justify-between">
                                <span class="text-[8px] font-bold uppercase tracking-[0.16em] text-slate-500">Jadwal</span>
                                <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                            </div>
                            <p class="mt-2 text-[11px] font-bold">Algoritma</p>
                            <div class="mt-2 space-y-1.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                                    <span class="h-1 flex-1 rounded-full bg-slate-100"></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                                    <span class="h-1 flex-1 rounded-full bg-slate-100"></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-200"></span>
                                    <span class="h-1 flex-1 rounded-full bg-slate-100"></span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="hero-3d-card z-10 w-40 shrink-0 rounded-2xl bg-white p-3 text-[#0B0B0C] shadow-[0_30px_70px_-20px_rgba(3,27,60,.6)] ring-1 ring-black/5 xl:w-44">
                            <div class="flex items-center justify-between">
                                <span class="text-[8px] font-bold uppercase tracking-[0.16em] text-slate-500">Nilai</span>
                                <span
                                    class="rounded-full bg-[#E8F9BE] px-2 py-0.5 text-[7px] font-bold uppercase tracking-wider text-[#3F5B00]">Bagus</span>
                            </div>
                            <div class="mt-2 flex items-end justify-between">
                                <p class="text-2xl font-extrabold tracking-tight">92<span
                                        class="text-[11px] font-semibold text-slate-500">/100</span></p>
                                <span class="text-[9px] font-bold text-emerald-600">+8</span>
                            </div>
                            <div class="mt-3 space-y-1.5">
                                <div class="h-1.5 w-full rounded-full bg-slate-100"><span
                                        class="block h-1.5 w-[92%] rounded-full bg-[#0A66C8]"></span></div>
                                <div class="h-1.5 w-full rounded-full bg-slate-100"><span
                                        class="block h-1.5 w-[74%] rounded-full bg-[#C8F24D]"></span></div>
                            </div>
                        </div>

                        <div
                            class="hero-3d-card hidden w-32 shrink-0 rotate-6 rounded-2xl bg-[#0B0B0C] p-3 text-white shadow-[0_24px_60px_-24px_rgba(3,27,60,.55)] ring-1 ring-white/10 xl:block xl:w-36">
                            <div class="flex items-center justify-between">
                                <span class="text-[8px] font-bold uppercase tracking-[0.16em] text-white/50">Presensi</span>
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-[#C8F24D] text-[#0B0B0C]">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" aria-hidden="true">
                                        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.6"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                            <div class="mt-2 grid place-items-center rounded-xl bg-white/5 p-2">
                                <svg viewBox="0 0 32 32" fill="currentColor" class="h-12 w-12 text-white/90"
                                    aria-hidden="true">
                                    <path
                                        d="M4 4h8v8H4V4zm2 2v4h4V6H6zM20 4h8v8h-8V4zm2 2v4h4V6h-4zM4 20h8v8H4v-8zm2 2v4h4v-4H6z" />
                                    <path
                                        d="M16 4h2v2h-2zM16 8h2v2h-2zM16 12h2v2h-2zM20 14h2v2h-2zM24 14h2v2h-2zM28 14h2v2h-2zM16 18h2v2h-2zM20 20h2v2h-2zM24 20h2v2h-2zM28 20h2v2h-2zM18 22h2v2h-2zM22 24h2v2h-2zM26 24h2v2h-2zM16 26h2v2h-2zM20 28h2v2h-2zM26 28h2v2h-2z" />
                                </svg>
                            </div>
                            <p class="mt-2 text-center text-[8px] font-semibold text-[#C8F24D]">Tercatat otomatis</p>
                        </div>
                        </div>
                    </div>

                    <div data-aos="fade-up" data-aos-delay="300" data-aos-offset="0" data-aos-once="true"
                        class="mt-4 flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.2em] text-[#0B3E6B]/70">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        Presensi real-time · nilai transparan
                    </div>
                </div>
            </aside>

            <main class="flex flex-col bg-white px-6 py-7 sm:px-10 lg:px-14">
                <div class="mx-auto flex w-full max-w-sm flex-1 flex-col justify-center py-8 lg:py-4">
                    {{ $slot }}
                </div>

                <div data-aos="fade-up" data-aos-offset="0" data-aos-once="true"
                    class="mx-auto flex w-full max-w-sm items-center justify-between gap-4 border-t border-black/5 pt-5 text-[10px] text-slate-500">
                    <span>&copy; {{ date('Y') }} SI-PROG</span>
                    <a href="mailto:{{ config('portal.email') }}"
                        class="font-semibold transition hover:text-[#0A66C8]">Butuh bantuan?</a>
                </div>
            </main>
        </div>
    </div>
</body>

</html>
