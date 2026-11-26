@php
    $navBase = 'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-[12.5px] font-semibold transition';
    $navOn = 'bg-[#0B0B0C] text-white shadow-[0_12px_28px_-18px_rgba(11,11,12,.85)]';
    $navOff = 'text-slate-600 hover:bg-[#F5F5F2] hover:text-[#0B0B0C]';
    $iconBase = 'h-[17px] w-[17px] shrink-0 transition';
    $iconOn = 'text-[#C8F24D]';
    $iconOff = 'text-slate-500 group-hover:text-[#0A66C8]';
    $label = 'px-3 pb-1.5 pt-5 text-[9px] font-extrabold uppercase tracking-[0.2em] text-slate-500';
@endphp

<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-[#0B0B0C]/40 backdrop-blur-sm lg:hidden"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-[17.5rem] flex-col overflow-hidden border-r border-black/5 bg-white p-3 transition-transform duration-300 lg:sticky lg:top-3 lg:z-auto lg:h-[calc(100svh-1.5rem)] lg:w-72 lg:translate-x-0 lg:self-start lg:rounded-[26px] lg:border lg:shadow-[0_24px_60px_-48px_rgba(11,11,12,.55)]">

    <div class="flex items-center justify-between gap-3 px-2 pb-4 pt-2">
        <a href="{{ route('portal') }}" class="flex items-center gap-2.5">
            <span
                class="grid h-10 w-10 place-items-center overflow-hidden rounded-full bg-white shadow-sm ring-1 ring-black/5">
                <img src="{{ asset('assets/logo1x1.png') }}" alt="" width="32" height="32"
                    class="h-9 w-9 object-contain" decoding="async">
            </span>
            <span class="min-w-0">
                <span class="block text-[15px] font-extrabold leading-tight tracking-tight">SI-PROG</span>
                <span class="block text-[9px] font-bold uppercase tracking-[0.16em] text-slate-500">Ruang
                    Praktikum</span>
            </span>
        </a>
        <button @click="sidebarOpen = false" aria-label="Tutup menu"
            class="grid h-8 w-8 place-items-center rounded-full border border-black/5 text-slate-500 transition hover:bg-[#F5F5F2] lg:hidden">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                <path d="m7 7 10 10M17 7 7 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <div class="mx-2 mb-1 flex items-center gap-2 rounded-full border border-black/5 bg-[#F5F5F2] px-3 py-2">
        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
        <span class="truncate text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
            {{ auth()->user()->role->label() }}
        </span>
        <span class="ml-auto shrink-0 font-mono text-[10px] text-slate-500">{{ auth()->user()->nim_nip }}</span>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-1 pb-2 pt-2 text-sm">
        <a href="{{ route('dashboard') }}"
            class="{{ $navBase }} {{ request()->routeIs('dashboard') ? $navOn : $navOff }}">
            <x-nav-icon name="dashboard" :class="$iconBase . ' ' . (request()->routeIs('dashboard') ? $iconOn : $iconOff)" />
            Dashboard
        </a>

        @if (auth()->user()->role === \App\Enums\UserRole::MAHASISWA)
            <p class="{{ $label }}">Menu Praktikum</p>
            <a href="{{ route('mahasiswa.presensi.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('mahasiswa.presensi.*') ? $navOn : $navOff }}">
                <x-nav-icon name="qr" :class="$iconBase . ' ' . (request()->routeIs('mahasiswa.presensi.*') ? $iconOn : $iconOff)" />
                Scan Presensi QR
            </a>
            <a href="{{ route('mahasiswa.pretest.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('mahasiswa.pretest.*') ? $navOn : $navOff }}">
                <x-nav-icon name="clock" :class="$iconBase . ' ' . (request()->routeIs('mahasiswa.pretest.*') ? $iconOn : $iconOff)" />
                Pre-Test (15 Menit)
            </a>
            <a href="{{ route('mahasiswa.tugas.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('mahasiswa.tugas.*') ? $navOn : $navOff }}">
                <x-nav-icon name="doc" :class="$iconBase . ' ' . (request()->routeIs('mahasiswa.tugas.*') ? $iconOn : $iconOff)" />
                Tugas Mingguan (PDF)
            </a>
            <a href="{{ route('mahasiswa.nilai.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('mahasiswa.nilai.*') ? $navOn : $navOff }}">
                <x-nav-icon name="chart" :class="$iconBase . ' ' . (request()->routeIs('mahasiswa.nilai.*') ? $iconOn : $iconOff)" />
                Transparansi Nilai
            </a>
        @endif

        @if (in_array(auth()->user()->role, [\App\Enums\UserRole::ASISTEN, \App\Enums\UserRole::ADMIN]))
            <p class="{{ $label }}">Operasional Lab</p>
            <a href="{{ route('sessions.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('sessions.*') || request()->routeIs('grading.*') ? $navOn : $navOff }}">
                <x-nav-icon name="calendar" :class="$iconBase . ' ' . (request()->routeIs('sessions.*') || request()->routeIs('grading.*') ? $iconOn : $iconOff)" />
                Sesi, QR & Penilaian
            </a>
            <a href="{{ route('admin.pretest.monitor') }}"
                class="{{ $navBase }} {{ request()->routeIs('admin.pretest.monitor*') ? $navOn : $navOff }}">
                <x-nav-icon name="eye" :class="$iconBase . ' ' . (request()->routeIs('admin.pretest.monitor*') ? $iconOn : $iconOff)" />
                Monitoring Pre-Test
            </a>
        @endif

        @if (in_array(auth()->user()->role, [\App\Enums\UserRole::ASISTEN, \App\Enums\UserRole::ADMIN]))
            <p class="{{ $label }}">Konten & Tugas</p>
            <a href="{{ route('admin.pretest.overview') }}"
                class="{{ $navBase }} {{ request()->routeIs('admin.pretest.*') && !request()->routeIs('admin.pretest.monitor*') ? $navOn : $navOff }}">
                <x-nav-icon name="doc" :class="$iconBase . ' ' . (request()->routeIs('admin.pretest.*') && !request()->routeIs('admin.pretest.monitor*') ? $iconOn : $iconOff)" />
                Soal Pre-Test
            </a>
            <a href="{{ route('admin.assignments.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('admin.assignments.*') ? $navOn : $navOff }}">
                <x-nav-icon name="upload" :class="$iconBase . ' ' . (request()->routeIs('admin.assignments.*') ? $iconOn : $iconOff)" />
                Upload Tugas Mingguan
            </a>
            <a href="{{ route('admin.submissions.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('admin.submissions.*') ? $navOn : $navOff }}">
                <x-nav-icon name="check" :class="$iconBase . ' ' . (request()->routeIs('admin.submissions.*') ? $iconOn : $iconOff)" />
                Periksa Tugas Mingguan
            </a>
            @if (auth()->user()->role === \App\Enums\UserRole::ADMIN)
                <a href="{{ route('admin.announcements.index') }}"
                    class="{{ $navBase }} {{ request()->routeIs('admin.announcements.*') ? $navOn : $navOff }}">
                    <x-nav-icon name="megaphone" :class="$iconBase . ' ' . (request()->routeIs('admin.announcements.*') ? $iconOn : $iconOff)" />
                    Buat Pemberitahuan
                </a>
            @endif
        @endif

        @if (auth()->user()->role === \App\Enums\UserRole::ADMIN)
            <p class="{{ $label }}">Data Master & Export</p>
            <a href="{{ route('admin.classrooms.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('admin.classrooms.*') ? $navOn : $navOff }}">
                <x-nav-icon name="building" :class="$iconBase . ' ' . (request()->routeIs('admin.classrooms.*') ? $iconOn : $iconOff)" />
                Data Kelas
            </a>
            <a href="{{ route('admin.master.students') }}"
                class="{{ $navBase }} {{ request()->routeIs('admin.master.students') ? $navOn : $navOff }}">
                <x-nav-icon name="users" :class="$iconBase . ' ' . (request()->routeIs('admin.master.students') ? $iconOn : $iconOff)" />
                Data Mahasiswa
            </a>
            <a href="{{ route('admin.master.assistants') }}"
                class="{{ $navBase }} {{ request()->routeIs('admin.master.assistants') ? $navOn : $navOff }}">
                <x-nav-icon name="badge" :class="$iconBase . ' ' . (request()->routeIs('admin.master.assistants') ? $iconOn : $iconOff)" />
                Data Asisten
            </a>
            <a href="{{ route('admin.grades.index') }}"
                class="{{ $navBase }} {{ request()->routeIs('admin.grades.*') ? $navOn : $navOff }}">
                <x-nav-icon name="table" :class="$iconBase . ' ' . (request()->routeIs('admin.grades.*') ? $iconOn : $iconOff)" />
                Data & Export Nilai
            </a>
        @endif

        <p class="{{ $label }}">Akun</p>
        <a href="{{ route('profile.edit') }}"
            class="{{ $navBase }} {{ request()->routeIs('profile.edit') ? $navOn : $navOff }}">
            <x-nav-icon name="user" :class="$iconBase . ' ' . (request()->routeIs('profile.edit') ? $iconOn : $iconOff)" />
            Profil Akun
        </a>
    </nav>

    <div class="mt-2 border-t border-black/5 px-1 pt-3">
        <a href="{{ route('portal') }}"
            class="group flex items-center justify-between gap-2 rounded-xl border border-black/5 bg-[#F5F5F2] px-3 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-slate-500 transition hover:border-black/10 hover:bg-white hover:text-[#0B0B0C]">
            Halaman Portal
            <x-nav-icon name="portal" class="h-3.5 w-3.5 text-slate-500 transition group-hover:text-[#0A66C8]" />
        </a>
    </div>
</aside>
