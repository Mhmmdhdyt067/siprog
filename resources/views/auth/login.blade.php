<x-guest-layout>
    <div data-aos="fade-up" data-aos-once="true" class="mb-8">
        <p class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500">
            <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
            Masuk
        </p>
        <h2 class="mt-3 text-balance text-[clamp(1.8rem,3.2vw,2.35rem)] font-semibold leading-[1.08] tracking-[-0.04em]">
            Selamat datang kembali</h2>
        <p class="mt-2 text-[13px] leading-relaxed text-slate-500">Mahasiswa dan asisten masuk dengan nama lengkap
            serta NIM. Admin masuk dengan email dan kata sandi.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" data-aos="reveal-3d" data-aos-delay="90"
        data-aos-once="true" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="login" value="Nama Lengkap / Email" />
            <div class="relative mt-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-slate-400">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.6" />
                        <path d="M5 20c0-3.4 3.1-6 7-6s7 2.6 7 6" stroke="currentColor" stroke-width="1.6"
                            stroke-linecap="round" />
                    </svg>
                </span>
                <x-text-input id="login" name="login" type="text" class="pl-11" :value="old('login')" required
                    autofocus autocomplete="username" placeholder="Nama lengkap atau email" />
            </div>
            <p class="mt-1.5 text-[10.5px] leading-relaxed text-slate-500">Gunakan nama lengkap sesuai data praktikum
                atau email yang terdaftar.</p>
            <x-input-error :messages="$errors->get('login')" class="mt-1.5" />
        </div>

        <div x-data="{ show: false }">
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" value="Kata sandi" />
            </div>
            <div class="relative mt-2">
                <span class="pointer-events-none absolute inset-y-0 left-0 grid w-11 place-items-center text-slate-400">
                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <rect x="4.5" y="10" width="15" height="10" rx="2.5" stroke="currentColor"
                            stroke-width="1.6" />
                        <path d="M8 10V8a4 4 0 018 0v2M12 14v2" stroke="currentColor" stroke-width="1.6"
                            stroke-linecap="round" />
                    </svg>
                </span>
                <x-text-input id="password" name="password" type="password" class="pl-11 pr-12" required
                    autocomplete="current-password" placeholder="NIM atau kata sandi"
                    x-bind:type="show ? 'text' : 'password'" />
                <button type="button" @click="show = !show" :aria-pressed="show"
                    :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                    class="absolute inset-y-0 right-0 grid w-11 place-items-center rounded-r-xl text-slate-400 transition hover:text-slate-600 focus:outline-none focus-visible:text-[#0A66C8]">
                    <svg x-show="!show" viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"
                            stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6" />
                    </svg>
                    <svg x-cloak x-show="show" viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                        <path d="M4 4l16 16M9.9 6.1A9.6 9.6 0 0112 5.5c6 0 9.5 6.5 9.5 6.5a17 17 0 01-3.3 4M6.4 7.6A17 17 0 002.5 12S6 18.5 12 18.5c1.2 0 2.3-.3 3.3-.7"
                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <label for="remember_me" class="flex w-fit cursor-pointer items-center gap-2.5 text-[12px] font-medium text-slate-500">
            <input id="remember_me" type="checkbox" name="remember"
                class="h-4 w-4 rounded border-black/20 text-[#0A66C8] focus:ring-[#0A66C8]/30">
            Ingat saya
        </label>

        <button type="submit"
            class="group flex w-full items-center justify-between gap-3 rounded-full bg-[#C8F24D] py-2 pl-6 pr-2 text-[10px] font-extrabold uppercase tracking-[0.16em] text-[#0B0B0C] transition hover:bg-[#DAFF73] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0A66C8]/40 focus-visible:ring-offset-2">
            Masuk ke dashboard
            <span
                class="grid h-9 w-9 place-items-center rounded-full bg-[#0B0B0C] text-white transition duration-300 group-hover:rotate-45">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" aria-hidden="true">
                    <path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </span>
        </button>
    </form>

    <p data-aos="fade-up" data-aos-delay="180" data-aos-once="true"
        class="mt-7 text-center text-[12px] leading-relaxed text-slate-500">
        Belum punya akun? <a href="mailto:{{ config('portal.email') }}"
            class="font-bold text-[#0A66C8] underline-offset-4 transition hover:underline">Hubungi admin
            laboratorium</a>
    </p>
</x-guest-layout>
