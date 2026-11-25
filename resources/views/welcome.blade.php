<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/favicon-64.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/apple-touch-icon.png') }}">
    <meta name="description"
        content="SI-PROG adalah portal praktikum pemrograman: jadwal kelas, presensi QR, pre-test, tugas, pengumuman, dan nilai dalam satu tempat.">
    <title>SI-PROG · Sistem Informasi Praktikum Pemrograman</title>
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

<body id="top" class="bg-[#F5F5F2] font-brand text-[#0B0B0C] antialiased">
    @php
        $creator = config('portal.creator');
        $contactPerson = config('portal.contact_person');
        $team = config('portal.team', []);
        $contactEmail = config('portal.email');
        $contactPhone = config('portal.phone');
        $waNumber = preg_replace('/\D/', '', (string) config('portal.whatsapp'));
        $socials = config('portal.socials', []);
        $enterRoute = auth()->check() ? route('dashboard') : route('login');
        $enterLabel = auth()->check() ? 'Dashboard' : 'Masuk Sistem';
    @endphp

    <div class="p-2 sm:p-3">
        <header
            data-hero-3d
            class="relative isolate overflow-hidden rounded-[28px] bg-[linear-gradient(178deg,#0B6FD3_0%,#1B93EC_40%,#6ACBFA_76%,#B4E8FF_100%)] text-white sm:rounded-[36px]">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -left-32 top-1/4 h-[30rem] w-[30rem] rounded-full bg-white/20 blur-3xl"></div>
                <div class="absolute -right-24 top-1/3 h-[26rem] w-[26rem] rounded-full bg-white/25 blur-3xl"></div>
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_50%_36%,rgba(4,45,98,.26),transparent_60%)]"></div>
                <div class="absolute -bottom-40 left-1/2 h-[26rem] w-[120%] -translate-x-1/2 rounded-[50%] bg-white/40 blur-3xl"></div>
                <div class="absolute bottom-[26%] left-[6%] h-24 w-56 rounded-full bg-white/40 blur-2xl"></div>
                <div class="absolute bottom-[30%] right-[8%] h-20 w-48 rounded-full bg-white/35 blur-2xl"></div>
                <div class="absolute bottom-[8%] left-[32%] h-28 w-72 rounded-full bg-white/45 blur-3xl"></div>
                <div
                    class="absolute inset-x-0 bottom-0 h-2/3 bg-[radial-gradient(ellipse_at_18%_100%,rgba(255,255,255,.75),transparent_52%),radial-gradient(ellipse_at_72%_100%,rgba(255,255,255,.6),transparent_48%),radial-gradient(ellipse_at_45%_112%,rgba(255,255,255,.85),transparent_55%)]">
                </div>
            </div>

            <div class="relative z-10 mx-auto flex min-h-[calc(100svh-1.5rem)] w-full max-w-7xl flex-col px-4 sm:px-8">
                <nav class="flex items-center justify-between gap-4 py-4 sm:py-5" x-data="{ open: false }"
                    @click.outside="open = false">
                    <a href="{{ route('portal') }}" class="flex items-center gap-2.5">
                        <span
                            class="grid h-10 w-10 place-items-center overflow-hidden rounded-full bg-white shadow-sm ring-1 ring-white/60">
                            <img src="{{ asset('assets/logo1x1.png') }}" alt="" width="32" height="32"
                                class="h-9 w-9 object-contain" decoding="async">
                        </span>
                        <span class="text-[17px] font-extrabold tracking-tight">SI-PROG</span>
                    </a>

                    <div class="hidden items-center gap-8 md:flex">
                        <a href="#jadwal"
                            class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/90 transition hover:text-white">Jadwal</a>
                        <a href="#pengumuman"
                            class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/90 transition hover:text-white">Pemberitahuan</a>
                        <a href="#fitur"
                            class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/90 transition hover:text-white">Fitur</a>
                        <a href="#kontak"
                            class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/90 transition hover:text-white">Kontak</a>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ $enterRoute }}"
                            class="hidden rounded-full bg-[#C8F24D] px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#0B0B0C] transition hover:bg-[#DAFF73] sm:inline-flex">{{ $enterLabel }}</a>
                        <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="menu-portal"
                            class="grid h-10 w-10 place-items-center rounded-full border border-white/25 bg-white/10 backdrop-blur md:hidden">
                            <span class="sr-only">Buka menu</span>
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                                <path x-show="!open" d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                                <path x-cloak x-show="open" d="M6 6l12 12M18 6L6 18" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <div id="menu-portal" x-cloak x-show="open" x-transition.opacity.duration.150ms
                        class="absolute left-4 right-4 top-[4.4rem] z-30 rounded-2xl border border-black/5 bg-white p-2 text-[#0B0B0C] shadow-2xl md:hidden">
                        <a href="#jadwal" @click="open = false"
                            class="block rounded-xl px-4 py-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-600 transition hover:bg-[#F5F5F2]">Jadwal</a>
                        <a href="#pengumuman" @click="open = false"
                            class="block rounded-xl px-4 py-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-600 transition hover:bg-[#F5F5F2]">Pemberitahuan</a>
                        <a href="#fitur" @click="open = false"
                            class="block rounded-xl px-4 py-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-600 transition hover:bg-[#F5F5F2]">Fitur</a>
                        <a href="#kontak" @click="open = false"
                            class="block rounded-xl px-4 py-3 text-[11px] font-bold uppercase tracking-[0.16em] text-slate-600 transition hover:bg-[#F5F5F2]">Kontak</a>
                        <a href="{{ $enterRoute }}"
                            class="mt-1 block rounded-xl bg-[#C8F24D] px-4 py-3 text-center text-[11px] font-extrabold uppercase tracking-[0.16em] text-[#0B0B0C]">{{ $enterLabel }}</a>
                    </div>
                </nav>

                <div class="flex flex-1 flex-col items-center justify-center py-8 text-center sm:py-12">
                    <span data-aos="fade-up"
                        class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3.5 py-1.5 text-[9px] font-bold uppercase tracking-[0.2em] text-white/90 backdrop-blur">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        Sistem Informasi Praktikum
                    </span>

                    <h1 data-aos="fade-up" data-aos-delay="90"
                        class="mt-6 max-w-4xl text-balance text-[clamp(2.2rem,5.6vw,4.2rem)] font-semibold leading-[1.04] tracking-[-0.045em]">
                        Satu portal untuk seluruh<br class="hidden sm:block"> praktikum pemrograman
                    </h1>

                    <p data-aos="fade-up" data-aos-delay="170" class="mt-5 max-w-xl text-pretty text-[13px] leading-relaxed text-white/90 sm:text-[15px]">
                        Jadwal, presensi QR, pre-test, tugas, hingga nilai per pertemuan. Semuanya rapi dalam satu
                        tempat, tanpa perlu dicari di banyak grup.
                    </p>

                    <div data-aos="fade-up" data-aos-delay="250"
                        class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <a href="#jadwal"
                            class="rounded-full border border-white/30 bg-white/10 px-6 py-3 text-[10px] font-extrabold uppercase tracking-[0.16em] text-white backdrop-blur-md transition hover:bg-white/20">Lihat
                            Jadwal</a>
                        <a href="{{ $enterRoute }}"
                            class="group flex items-center gap-3 rounded-full bg-[#C8F24D] py-2 pl-6 pr-2 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#0B0B0C] transition hover:bg-[#DAFF73]">
                            {{ $enterLabel }}
                            <span
                                class="grid h-8 w-8 place-items-center rounded-full bg-[#0B0B0C] text-white transition duration-300 group-hover:rotate-45">
                                <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                    <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </a>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-delay="280"
                    class="hero-3d-stage relative mx-auto hidden h-[15rem] w-full max-w-5xl sm:block"
                    aria-hidden="true">
                    <div class="hero-3d-inner absolute inset-0">
                    <div
                        class="hero-3d-card absolute left-[8%] top-16 hidden w-40 rotate-[-16deg] rounded-2xl bg-white p-3 text-[#0B0B0C] shadow-[0_24px_60px_-24px_rgba(3,27,60,.55)] ring-1 ring-black/5 lg:block">
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
                        <div class="mt-3 flex items-center justify-between text-[8px] font-medium text-slate-500">
                            <span>Senin</span><span>08:00</span>
                        </div>
                    </div>

                    <div
                        class="hero-3d-card absolute left-[10%] top-4 w-36 rotate-[-8deg] lg:left-[26%] rounded-2xl bg-white p-2 text-[#0B0B0C] shadow-[0_24px_60px_-24px_rgba(3,27,60,.55)] ring-1 ring-black/5">
                        <div class="relative h-24 overflow-hidden rounded-xl bg-[linear-gradient(150deg,#0A66C8,#7BD0FB)]">
                            <span
                                class="absolute bottom-2 left-2 grid h-8 w-8 place-items-center rounded-full bg-white/95 text-[10px] font-extrabold text-[#0A66C8]">RA</span>
                            <span
                                class="absolute right-2 top-2 rounded-full bg-white/20 px-2 py-0.5 text-[7px] font-bold uppercase tracking-wider text-white">TI-2A</span>
                        </div>
                        <p class="mt-2 text-[10px] font-bold">Rani A.</p>
                        <p class="text-[8px] text-slate-500">Hadir · 08:02</p>
                    </div>

                    <div
                        class="hero-3d-card absolute left-1/2 top-0 z-10 w-44 -translate-x-1/2 rounded-2xl bg-white p-3 text-[#0B0B0C] shadow-[0_30px_70px_-20px_rgba(3,27,60,.6)] ring-1 ring-black/5">
                        <div class="flex items-center justify-between">
                            <span class="text-[8px] font-bold uppercase tracking-[0.16em] text-slate-500">Nilai</span>
                            <span
                                class="rounded-full bg-[#EAF9C4] px-2 py-0.5 text-[7px] font-bold uppercase tracking-wider text-[#3F5B00]">Bagus</span>
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
                            <div class="h-1.5 w-full rounded-full bg-slate-100"><span
                                    class="block h-1.5 w-[86%] rounded-full bg-slate-300"></span></div>
                        </div>
                    </div>

                    <div
                        class="hero-3d-card absolute left-[66%] top-4 w-36 rotate-[8deg] lg:left-[58%] rounded-2xl bg-white p-3 text-[#0B0B0C] shadow-[0_24px_60px_-24px_rgba(3,27,60,.55)] ring-1 ring-black/5">
                        <p class="text-[8px] font-bold uppercase tracking-[0.16em] text-slate-500">Presensi QR</p>
                        <div class="mt-2 grid place-items-center rounded-xl bg-[#0B0B0C] p-3">
                            <svg viewBox="0 0 32 32" fill="currentColor" class="h-14 w-14 text-white" aria-hidden="true">
                                <path
                                    d="M4 4h8v8H4V4zm2 2v4h4V6H6zM20 4h8v8h-8V4zm2 2v4h4V6h-4zM4 20h8v8H4v-8zm2 2v4h4v-4H6z" />
                                <path
                                    d="M16 4h2v2h-2zM16 8h2v2h-2zM16 12h2v2h-2zM20 14h2v2h-2zM24 14h2v2h-2zM28 14h2v2h-2zM16 18h2v2h-2zM20 20h2v2h-2zM24 20h2v2h-2zM28 20h2v2h-2zM18 22h2v2h-2zM22 24h2v2h-2zM26 24h2v2h-2zM16 26h2v2h-2zM20 28h2v2h-2zM26 28h2v2h-2z" />
                            </svg>
                        </div>
                        <p class="mt-2 text-center text-[8px] font-semibold text-emerald-600">Tercatat otomatis</p>
                    </div>

                    <div
                        class="hero-3d-card absolute left-[74%] top-16 hidden w-40 rotate-[16deg] rounded-2xl bg-[#0B0B0C] p-3 text-white shadow-[0_24px_60px_-24px_rgba(3,27,60,.55)] ring-1 ring-white/10 lg:block">
                        <div class="flex items-start justify-between">
                            <span class="text-[8px] font-bold uppercase tracking-[0.16em] text-white/50">Pemberitahuan</span>
                            <span class="grid h-6 w-6 place-items-center rounded-full bg-[#C8F24D] text-[#0B0B0C]">
                                <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" aria-hidden="true">
                                    <path d="M12 3a5 5 0 00-5 5v3l-1.5 3h13L17 11V8a5 5 0 00-5-5zM10 18a2 2 0 004 0"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-2 text-[10px] font-semibold leading-snug">Tugas 3 · Struktur Data</p>
                        <p class="mt-1 text-[8px] text-white/50">Batas 2 hari lagi</p>
                        <div class="mt-2 h-1 rounded-full bg-white/10">
                            <div class="h-1 w-2/3 rounded-full bg-[#C8F24D]"></div>
                        </div>
                    </div>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-delay="340"
                    class="flex flex-col items-center gap-2.5 pb-8 pt-8 text-center sm:pt-4">
                    <div class="flex items-center gap-1.5" aria-hidden="true">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        <span class="h-1.5 w-1.5 rounded-full bg-[#0B3E6B]/20"></span>
                    </div>
                    <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-[#0B3E6B]/70">
                        {{ $classrooms->count() }} kelas aktif · presensi real-time · nilai transparan
                    </p>
                </div>
            </div>
        </header>
    </div>

    <div data-aos="fade-up"
        class="mx-auto flex max-w-5xl flex-wrap items-center justify-center gap-x-8 gap-y-3 px-4 py-7 sm:py-9">
        <span class="text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500">Kelas Berjalan</span>
        <span class="hidden h-4 w-px bg-black/10 sm:block" aria-hidden="true"></span>
        @forelse ($classrooms->pluck('course_name')->unique()->take(6) as $course)
            <span class="flex items-center gap-2 text-[12px] font-semibold text-slate-500 sm:text-[13px]">
                <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5 text-slate-300" aria-hidden="true">
                    <path d="M12 3v18M3 12h18M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" />
                </svg>
                {{ $course }}
            </span>
        @empty
            <span class="text-[12px] font-semibold text-slate-500">Jadwal praktikum semester ini</span>
        @endforelse
    </div>

    <section class="px-4 py-16 sm:py-24">
        <div class="mx-auto max-w-5xl">
            <p data-aos="fade-up"
                class="flex items-center justify-center gap-2 text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500">
                <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                Tentang
            </p>
            <h2 data-aos="fade-up" data-aos-delay="80"
                class="mt-7 text-balance text-center text-[clamp(1.8rem,4.2vw,3.35rem)] font-medium leading-[1.14] tracking-[-0.035em] text-[#0B0B0C]">
                Praktikum jadi lebih <span class="text-transparent [-webkit-text-stroke:1.4px_#7C828A]">rapi</span>, <span
                    class="text-transparent [-webkit-text-stroke:1.4px_#7C828A]">terukur</span>, dan <span class="text-transparent [-webkit-text-stroke:1.4px_#7C828A]">transparan</span> — dari
                <span
                    class="mx-1 inline-grid h-[0.92em] w-[0.92em] translate-y-[0.06em] place-items-center rounded-full bg-[#0A66C8] align-middle text-white"
                    aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" class="h-[54%] w-[54%]">
                        <path d="M5 12h14M12 5v14" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" />
                    </svg>
                </span>
                presensi hingga
                <span
                    class="mx-1 inline-grid h-[0.92em] w-[0.92em] translate-y-[0.06em] place-items-center rounded-full bg-[#C8F24D] align-middle text-[#0B0B0C]"
                    aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" class="h-[54%] w-[54%]">
                        <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
                nilai, tanpa salin-menyalin di grup chat.
            </h2>
        </div>
    </section>

    <section id="jadwal" class="scroll-mt-8 px-4 pb-16 sm:pb-24">
        <div class="mx-auto max-w-6xl">
            <div class="flex flex-col items-center text-center">
                <p data-aos="fade-up"
                    class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#0A66C8]"></span>
                    Jadwal
                </p>
                <h2 data-aos="fade-up" data-aos-delay="70"
                    class="mt-4 text-[clamp(1.7rem,3.4vw,2.6rem)] font-semibold tracking-[-0.035em]">Kelas &amp; jadwal
                    praktikum</h2>
                <p data-aos="fade-up" data-aos-delay="140"
                    class="mt-3 max-w-md text-[13px] leading-relaxed text-slate-500">Temukan kelasmu, jam pelaksanaan,
                    dan ruangan laboratorium tanpa bertanya ulang.</p>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                @forelse ($classrooms as $kelas)
                    <div data-aos="reveal-3d" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                    <article
                        class="group h-full rounded-[26px] border border-black/5 bg-white p-6 transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_24px_48px_-32px_rgba(11,11,12,.4)] sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <span
                                class="rounded-full bg-[#EAF5FF] px-3 py-1 text-[9px] font-extrabold uppercase tracking-[0.16em] text-[#0A66C8]">{{ $kelas->code }}</span>
                            <span class="grid h-9 w-9 place-items-center rounded-full bg-[#F5F5F2] text-slate-500">
                                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                                    <path d="M7 3v3M17 3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"
                                        stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <h3 class="mt-5 text-[21px] font-bold leading-tight tracking-[-0.025em]">
                            {{ $kelas->course_name }}</h3>
                        <p class="mt-1 text-[12px] font-medium text-slate-500">Kelas {{ $kelas->name }}</p>
                        <dl class="mt-5 grid grid-cols-2 gap-x-4 gap-y-4 border-t border-black/5 pt-5">
                            <div>
                                <dt class="text-[8px] font-bold uppercase tracking-[0.18em] text-slate-500">Hari</dt>
                                <dd class="mt-1 text-[13px] font-semibold">{{ $kelas->schedule_day }}</dd>
                            </div>
                            <div>
                                <dt class="text-[8px] font-bold uppercase tracking-[0.18em] text-slate-500">Waktu</dt>
                                <dd class="mt-1 text-[13px] font-semibold">{{ $kelas->schedule_time }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-[8px] font-bold uppercase tracking-[0.18em] text-slate-500">Ruangan</dt>
                                <dd class="mt-1 flex items-center gap-2 text-[13px] font-semibold">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                                    {{ $kelas->room }}
                                </dd>
                            </div>
                        </dl>
                    </article>
                    </div>
                @empty
                    <p data-aos="fade-up"
                        class="col-span-full rounded-[26px] border border-dashed border-black/10 bg-white/60 p-10 text-center text-[13px] text-slate-500">
                        Belum ada kelas yang dijadwalkan.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section id="pengumuman" class="scroll-mt-8 px-4 pb-16 sm:pb-24">
        <div class="mx-auto max-w-6xl">
            <div class="flex flex-col items-center text-center">
                <p data-aos="fade-up"
                    class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                    Pemberitahuan
                </p>
                <h2 data-aos="fade-up" data-aos-delay="70"
                    class="mt-4 text-[clamp(1.7rem,3.4vw,2.6rem)] font-semibold tracking-[-0.035em]">Info tugas &amp;
                    pengumuman</h2>
                <p data-aos="fade-up" data-aos-delay="140"
                    class="mt-3 max-w-md text-[13px] leading-relaxed text-slate-500">Informasi resmi dari admin
                    laboratorium, selalu terbaru.</p>
            </div>

            <div class="mt-10 grid gap-4 md:grid-cols-2">
                @forelse ($announcements as $info)
                    <article data-aos="reveal-3d" data-aos-delay="{{ ($loop->index % 2) * 90 }}"
                        class="h-full rounded-[26px] border border-black/5 bg-white p-6 sm:p-7">
                        <div class="flex items-center justify-between gap-4">
                            <span
                                class="rounded-full px-3 py-1 text-[8px] font-extrabold uppercase tracking-[0.16em] {{ $info->type === 'tugas' ? 'bg-[#E8F9BE] text-[#3F5B00]' : 'bg-[#EAF5FF] text-[#0A66C8]' }}">{{ $info->type }}</span>
                            <span class="text-[10px] font-medium text-slate-500">{{ $info->created_at->locale('id')->diffForHumans() }}</span>
                        </div>
                        <h3 class="mt-4 text-[19px] font-bold leading-snug tracking-[-0.02em]">{{ $info->title }}</h3>
                        <p class="mt-2 line-clamp-3 text-[13px] leading-relaxed text-slate-500">{{ $info->content }}</p>
                        <div class="mt-5 flex items-center justify-between border-t border-black/5 pt-4">
                            <p class="text-[10px] font-semibold text-slate-500">Target:
                                {{ $info->classroom?->name ?? 'Semua Kelas' }}</p>
                            <span class="grid h-7 w-7 place-items-center rounded-full bg-[#F5F5F2] text-slate-500">
                                <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                    <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="1.8"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </article>
                @empty
                    <p data-aos="fade-up"
                        class="col-span-full rounded-[26px] border border-dashed border-black/10 bg-white/60 p-10 text-center text-[13px] text-slate-500">
                        Belum ada pemberitahuan.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section id="fitur" class="scroll-mt-8 px-4 pb-16 sm:pb-24">
        <div class="mx-auto max-w-6xl">
            <div class="flex flex-col items-center text-center">
                <p data-aos="fade-up"
                    class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                    Fitur
                </p>
                <h2 data-aos="fade-up" data-aos-delay="70"
                    class="mt-4 text-balance text-[clamp(1.7rem,3.4vw,2.6rem)] font-semibold tracking-[-0.035em]">
                    Semua kebutuhan praktikum, satu alur.</h2>
                <p data-aos="fade-up" data-aos-delay="140"
                    class="mt-3 max-w-lg text-[13px] leading-relaxed text-slate-500">Dirancang untuk mahasiswa, asisten,
                    dan admin — dari sebelum kelas dimulai sampai nilai keluar.</p>
            </div>

            <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <article data-aos="reveal-3d"
                    class="relative flex h-full flex-col overflow-hidden rounded-[26px] bg-[#0A66C8] p-6 text-white sm:p-7 md:row-span-2">
                    <span
                        class="w-fit rounded-full bg-white/15 px-3 py-1 text-[8px] font-extrabold uppercase tracking-[0.16em] text-white/90">Presensi</span>
                    <h3 class="mt-5 text-[26px] font-semibold leading-[1.08] tracking-[-0.035em]">Scan QR, kehadiran
                        langsung tercatat.</h3>
                    <p class="mt-3 max-w-xs text-[13px] leading-relaxed text-white/70">Kode QR tampil di proyektor
                        kelas. Mahasiswa memindai dari HP, dan presensi masuk tanpa absen manual.</p>

                    <div class="mt-7 rounded-2xl bg-white p-4 text-[#0B0B0C]">
                        <div class="flex items-center justify-between">
                            <p class="text-[8px] font-bold uppercase tracking-[0.18em] text-slate-500">Sesi hari ini</p>
                            <span
                                class="rounded-full bg-[#C8F24D] px-2 py-0.5 text-[7px] font-extrabold uppercase tracking-wider text-[#0B0B0C]">Aktif</span>
                        </div>
                        <div class="mt-4 flex items-center gap-4">
                            <div class="grid h-[5.5rem] w-[5.5rem] shrink-0 place-items-center rounded-xl bg-[#0B0B0C] p-2">
                                <svg viewBox="0 0 32 32" fill="currentColor" class="h-14 w-14 text-white"
                                    aria-hidden="true">
                                    <path
                                        d="M4 4h8v8H4V4zm2 2v4h4V6H6zM20 4h8v8h-8V4zm2 2v4h4V6h-4zM4 20h8v8H4v-8zm2 2v4h4v-4H6z" />
                                    <path
                                        d="M16 4h2v2h-2zM16 8h2v2h-2zM16 12h2v2h-2zM20 14h2v2h-2zM24 14h2v2h-2zM28 14h2v2h-2zM16 18h2v2h-2zM20 20h2v2h-2zM24 20h2v2h-2zM28 20h2v2h-2zM18 22h2v2h-2zM22 24h2v2h-2zM26 24h2v2h-2zM16 26h2v2h-2zM20 28h2v2h-2zM26 28h2v2h-2z" />
                                </svg>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="h-1.5 w-4/5 rounded-full bg-slate-100"></div>
                                <div class="h-1.5 w-3/5 rounded-full bg-slate-100"></div>
                                <div class="flex items-center gap-1.5 pt-1">
                                    <span
                                        class="grid h-4 w-4 place-items-center rounded-full bg-[#E8F9BE] text-[#3F5B00]">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-2.5 w-2.5">
                                            <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span class="text-[9px] font-semibold text-slate-500">Kehadiran tercatat
                                        otomatis</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto flex flex-wrap gap-2 pt-6">
                        <span
                            class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[8px] font-bold uppercase tracking-[0.12em]">QR
                            proyektor</span>
                        <span
                            class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[8px] font-bold uppercase tracking-[0.12em]">QR bisa
                            diperbarui</span>
                    </div>
                </article>

                <article data-aos="reveal-3d" data-aos-delay="90"
                    class="flex h-full flex-col rounded-[26px] bg-[#F0F1EF] p-6 sm:p-7">
                    <div class="flex items-center justify-between">
                        <span
                            class="rounded-full bg-white px-3 py-1 text-[8px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Pre-Test</span>
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-white text-[#0A66C8]">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                                <circle cx="12" cy="13" r="7" stroke="currentColor" stroke-width="1.6" />
                                <path d="M12 10v3.5l2 1.5M9 3h6" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-6 text-[44px] font-extrabold leading-none tracking-[-0.05em]">15<span
                            class="ml-1 text-[15px] font-bold tracking-tight text-slate-500">menit</span></p>
                    <p class="mt-3 text-[13px] leading-relaxed text-slate-500">Soal pre-test tampil dengan timer
                        15 menit. Akses ditutup otomatis saat waktu habis.</p>
                    <div class="mt-auto pt-6">
                        <div class="h-1.5 w-full rounded-full bg-black/5">
                            <div class="h-1.5 w-2/3 rounded-full bg-[#0A66C8]"></div>
                        </div>
                    </div>
                </article>

                <article data-aos="reveal-3d" data-aos-delay="150"
                    class="h-full rounded-[26px] bg-[#C8F24D] p-6 text-[#0B0B0C] sm:p-7">
                    <div class="flex items-center justify-between">
                        <span
                            class="rounded-full bg-black/5 px-3 py-1 text-[8px] font-extrabold uppercase tracking-[0.16em] text-black/60">Nilai</span>
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-[#0B0B0C] text-[#C8F24D]">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M4 19V9M10 19V5M16 19v-7M22 19H2" stroke="currentColor" stroke-width="1.8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                    <p class="mt-6 text-[38px] font-extrabold leading-none tracking-[-0.05em]">Real-time</p>
                    <p class="mt-3 text-[13px] leading-relaxed text-black/60">Nilai per pertemuan terbuka untuk
                        mahasiswa. Tidak perlu menunggu rekap akhir semester.</p>
                    <div class="mt-6 space-y-2">
                        <div class="flex items-center justify-between rounded-xl bg-white/45 px-3 py-2 text-[10px] font-bold">
                            <span>Pre-test</span><span>90</span>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-white/45 px-3 py-2 text-[10px] font-bold">
                            <span>Tugas</span><span>95</span>
                        </div>
                    </div>
                </article>

                <article data-aos="reveal-3d" data-aos-delay="120"
                    class="h-full rounded-[26px] bg-[#0B0B0C] p-6 text-white sm:p-7">
                    <div class="flex items-center justify-between">
                        <span
                            class="rounded-full border border-white/15 px-3 py-1 text-[8px] font-extrabold uppercase tracking-[0.16em] text-white/60">Pemberitahuan</span>
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-[#C8F24D] text-[#0B0B0C]">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M12 3a5 5 0 00-5 5v3l-1.5 3h13L17 11V8a5 5 0 00-5-5zM10 18a2 2 0 004 0"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                    <h3 class="mt-6 text-[22px] font-semibold leading-tight tracking-[-0.025em]">Tugas &amp; info, tidak
                        terlewat.</h3>
                    <p class="mt-3 text-[13px] leading-relaxed text-white/55">Pengumuman admin dan tenggat tugas tampil
                        di satu tempat, lengkap dengan target kelas.</p>
                </article>

                <article data-aos="reveal-3d" data-aos-delay="210"
                    class="h-full rounded-[26px] border border-black/5 bg-white p-6 sm:p-7">
                    <div class="flex items-center justify-between">
                        <span
                            class="rounded-full bg-[#F5F5F2] px-3 py-1 text-[8px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Jadwal</span>
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-[#F5F5F2] text-[#0A66C8]">
                            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                                <path d="M7 3v3M17 3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"
                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                    <h3 class="mt-6 text-[22px] font-semibold leading-tight tracking-[-0.025em]">Kelas &amp; ruangan
                        jelas.</h3>
                    <p class="mt-3 text-[13px] leading-relaxed text-slate-500">Hari, jam, dan laboratorium setiap kelas
                        tercatat rapi.</p>
                    <div class="mt-5 flex flex-wrap gap-1.5">
                        @foreach ($classrooms->take(2) as $kelas)
                            <span
                                class="rounded-full bg-[#F5F5F2] px-2.5 py-1 text-[8px] font-bold uppercase tracking-[0.1em] text-slate-500">{{ $kelas->name }}</span>
                        @endforeach
                        @if ($classrooms->count() > 2)
                            <span
                                class="rounded-full bg-[#F5F5F2] px-2.5 py-1 text-[8px] font-bold uppercase tracking-[0.1em] text-slate-500">+{{ $classrooms->count() - 2 }}</span>
                        @endif
                    </div>
                </article>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <article data-aos="reveal-3d" data-aos-delay="0" class="h-full rounded-[22px] border border-black/5 bg-white/70 p-5">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-[#0B0B0C] text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                            <path d="M20 12.5V19a1 1 0 01-1 1H5a1 1 0 01-1-1v-6.5M12 15V4m0 0L8 8m4-4l4 4"
                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-[14px] font-bold tracking-[-0.01em]">Pengumpulan tugas</h3>
                    <p class="mt-1.5 text-[11px] leading-relaxed text-slate-500">Unggah berkas dengan tenggat yang
                        jelas.</p>
                </article>
                <article data-aos="reveal-3d" data-aos-delay="70" class="h-full rounded-[22px] border border-black/5 bg-white/70 p-5">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-[#0B0B0C] text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                            <path d="M4 5h16v11H9l-5 4V5z" stroke="currentColor" stroke-width="1.6"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-[14px] font-bold tracking-[-0.01em]">Feedback praktikum</h3>
                    <p class="mt-1.5 text-[11px] leading-relaxed text-slate-500">Rating dan catatan setelah sesi selesai.
                    </p>
                </article>
                <article data-aos="reveal-3d" data-aos-delay="140" class="h-full rounded-[22px] border border-black/5 bg-white/70 p-5">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-[#0B0B0C] text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                            <path d="M12 4v10m0 0l-4-4m4 4l4-4M5 19h14" stroke="currentColor" stroke-width="1.6"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-[14px] font-bold tracking-[-0.01em]">Ekspor nilai Excel</h3>
                    <p class="mt-1.5 text-[11px] leading-relaxed text-slate-500">Rekap per sesi atau keseluruhan.</p>
                </article>
                <article data-aos="reveal-3d" data-aos-delay="210" class="h-full rounded-[22px] border border-black/5 bg-white/70 p-5">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-[#0B0B0C] text-white">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                            <circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.6" />
                            <path d="M3.5 19a5.5 5.5 0 0111 0M16 5.5a3 3 0 010 5M17.5 19a5 5 0 00-2-4"
                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                        </svg>
                    </span>
                    <h3 class="mt-4 text-[14px] font-bold tracking-[-0.01em]">Tiga peran akses</h3>
                    <p class="mt-1.5 text-[11px] leading-relaxed text-slate-500">Admin, asisten, dan mahasiswa.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="px-4 pb-6 sm:pb-10">
        <div data-aos="reveal-3d"
            class="relative mx-auto max-w-6xl overflow-hidden rounded-[28px] bg-[#0B0B0C] px-6 py-12 text-white sm:rounded-[32px] sm:px-12 sm:py-14">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#C8F24D]/20 blur-3xl"
                aria-hidden="true"></div>
            <div
                class="relative z-10 flex flex-col items-center gap-8 text-center lg:flex-row lg:justify-between lg:text-left">
                <div>
                    <p class="flex items-center justify-center gap-2 text-[9px] font-bold uppercase tracking-[0.22em] text-[#C8F24D] lg:justify-start">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        Mulai Sekarang
                    </p>
                    <h2
                        class="mt-4 max-w-lg text-balance text-[clamp(1.8rem,3.6vw,2.8rem)] font-semibold leading-[1.1] tracking-[-0.035em]">
                        Siap masuk ke kelas praktikummu?</h2>
                    <p class="mt-3 max-w-md text-[13px] leading-relaxed text-white/65">Gunakan akun kampus untuk
                        melihat jadwal, mengisi presensi, dan memantau nilai.</p>
                </div>
                <a href="{{ $enterRoute }}"
                    class="group flex shrink-0 items-center gap-3 rounded-full bg-[#C8F24D] py-2 pl-6 pr-2 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#0B0B0C] transition hover:bg-[#DAFF73]">
                    {{ $enterLabel }}
                    <span
                        class="grid h-9 w-9 place-items-center rounded-full bg-[#0B0B0C] text-white transition duration-300 group-hover:rotate-45">
                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                            <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </section>

    <footer id="kontak" class="scroll-mt-8 px-2 pb-2 sm:px-3 sm:pb-3">
        <div class="rounded-[28px] bg-[#0B0B0C] text-white sm:rounded-[32px]">
            <div data-aos="fade-up" class="mx-auto max-w-6xl px-6 py-12 sm:px-10 sm:py-14">
                <div class="grid gap-10 md:grid-cols-12">
                    <div class="md:col-span-5">
                        <a href="#top" class="flex items-center gap-2.5">
                            <span
                                class="grid h-10 w-10 place-items-center overflow-hidden rounded-full bg-white ring-1 ring-white/10">
                                <img src="{{ asset('assets/logo1x1.png') }}" alt="" width="32" height="32"
                                    class="h-9 w-9 object-contain" decoding="async">
                            </span>
                            <span class="text-[17px] font-extrabold tracking-tight">SI-PROG</span>
                        </a>
                        <p class="mt-4 max-w-xs text-[13px] leading-relaxed text-white/60">
                            Sistem informasi praktikum pemrograman. Jadwal, presensi, pre-test, tugas, dan nilai dalam
                            satu portal.
                        </p>

                        <div class="mt-6">
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/50">Tim Pengembang</p>
                            <p class="mt-2 text-[13px] font-bold">{{ $creator }}</p>
                            <ul class="mt-2 space-y-1.5 text-[12px] text-white/60">
                                @foreach ($team as $member)
                                    <li class="flex flex-wrap items-center gap-2">
                                        <span>{{ $member['name'] }}</span>
                                        @if (($member['role'] ?? '') === 'Ketua')
                                            <span
                                                class="rounded-full bg-[#C8F24D]/15 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wide text-[#C8F24D]">Ketua</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @if (!empty($socials))
                            <div class="mt-6 flex items-center gap-2">
                                @if (!empty($socials['instagram']))
                                    <a href="{{ $socials['instagram'] }}" target="_blank" rel="noopener noreferrer"
                                        aria-label="Instagram"
                                        class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-white/70 transition hover:border-[#C8F24D] hover:text-[#C8F24D]">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                                            <rect x="3.5" y="3.5" width="17" height="17" rx="5"
                                                stroke="currentColor" stroke-width="1.6" />
                                            <circle cx="12" cy="12" r="4" stroke="currentColor"
                                                stroke-width="1.6" />
                                            <circle cx="17.2" cy="6.8" r="1" fill="currentColor" />
                                        </svg>
                                    </a>
                                @endif
                                @if (!empty($socials['github']))
                                    <a href="{{ $socials['github'] }}" target="_blank" rel="noopener noreferrer"
                                        aria-label="GitHub"
                                        class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-white/70 transition hover:border-[#C8F24D] hover:text-[#C8F24D]">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"
                                            aria-hidden="true">
                                            <path
                                                d="M12 2a10 10 0 00-3.16 19.49c.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.45-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.89 1.53 2.34 1.09 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.56-1.11-4.56-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02a9.5 9.5 0 015 0c1.91-1.29 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.68-4.57 4.93.36.31.68.92.68 1.85v2.74c0 .27.18.58.69.48A10 10 0 0012 2z" />
                                        </svg>
                                    </a>
                                @endif
                                @if (!empty($socials['linkedin']))
                                    <a href="{{ $socials['linkedin'] }}" target="_blank" rel="noopener noreferrer"
                                        aria-label="LinkedIn"
                                        class="grid h-10 w-10 place-items-center rounded-full border border-white/15 text-white/70 transition hover:border-[#C8F24D] hover:text-[#C8F24D]">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"
                                            aria-hidden="true">
                                            <path d="M6.5 8.5v10M6.5 5.5v.01M11 18.5v-6a3 3 0 016 0v6"
                                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                stroke-linejoin="round" fill="none" />
                                            <circle cx="6.5" cy="5.5" r="1.4" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="md:col-span-3">
                        <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/50">Navigasi</p>
                        <ul class="mt-4 space-y-2.5 text-[13px]">
                            <li><a href="#jadwal" class="text-white/75 transition hover:text-[#C8F24D]">Jadwal
                                    praktikum</a></li>
                            <li><a href="#pengumuman"
                                    class="text-white/75 transition hover:text-[#C8F24D]">Pemberitahuan</a></li>
                            <li><a href="#fitur" class="text-white/75 transition hover:text-[#C8F24D]">Fitur</a>
                            </li>
                            <li><a href="{{ $enterRoute }}"
                                    class="text-white/75 transition hover:text-[#C8F24D]">{{ $enterLabel }}</a></li>
                        </ul>
                    </div>

                    <div class="md:col-span-4">
                        <p class="text-[9px] font-bold uppercase tracking-[0.2em] text-white/50">Contact Person</p>
                        <div class="mt-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-center gap-3">
                                <span
                                    class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-[#C8F24D] text-[12px] font-extrabold text-[#0B0B0C]">MH</span>
                                <div class="min-w-0">
                                    <p class="truncate text-[14px] font-bold">{{ $contactPerson }}</p>
                                    <p class="truncate text-[10px] text-white/40">Contact Person &middot; {{ $creator }}</p>
                                </div>
                            </div>
                            <div class="mt-4 space-y-2.5">
                                <a href="mailto:{{ $contactEmail }}"
                                    class="group flex items-center gap-2.5 text-[12px] text-white/75 transition hover:text-[#C8F24D]">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 shrink-0"
                                        aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="14" rx="2"
                                            stroke="currentColor" stroke-width="1.6" />
                                        <path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.6"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="truncate">{{ $contactEmail }}</span>
                                </a>
                                @if ($waNumber !== '')
                                    <a href="https://wa.me/{{ $waNumber }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="group flex items-center gap-2.5 text-[12px] text-white/75 transition hover:text-[#C8F24D]">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 shrink-0"
                                            aria-hidden="true">
                                            <path d="M20 12a8 8 0 01-11.8 7L4 20l1-4.2A8 8 0 1120 12z"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M9 9.5c.3 2.6 2.4 4.7 5 5l1-1.5 2 .8-.4 1.6c-.2.8-1 1.2-1.8 1.1A7.2 7.2 0 018 10.5c-.1-.8.3-1.6 1.1-1.8L10.7 8l.8 2-1.5 1z"
                                                fill="currentColor" />
                                        </svg>
                                        <span class="truncate">{{ $contactPhone }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 sm:flex-row">
                    <p class="text-[11px] text-white/55">&copy; {{ date('Y') }} SI-PROG. Dibuat oleh
                        {{ $creator }}.</p>
                    <a href="#top"
                        class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.18em] text-white/55 transition hover:text-[#C8F24D]">
                        Kembali ke atas
                        <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                            <path d="M12 19V5m0 0l-5 5m5-5l5 5" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
