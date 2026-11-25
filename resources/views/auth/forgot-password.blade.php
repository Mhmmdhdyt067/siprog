<x-guest-layout>
    <div class="mb-8">
        <p class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500">
            <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
            Lupa kata sandi
        </p>
        <h2 class="mt-3 text-balance text-[clamp(1.8rem,3.2vw,2.35rem)] font-semibold leading-[1.08] tracking-[-0.04em]">
            Atur ulang akses akunmu</h2>
        <p class="mt-2 text-[13px] leading-relaxed text-slate-500">Masukkan email kampusmu. Kami akan mengirim tautan
            untuk membuat kata sandi baru.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2" type="email" name="email" :value="old('email')" required autofocus
                placeholder="nama@kampus.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <x-primary-button>Kirim tautan reset</x-primary-button>
    </form>

    <p class="mt-7 text-center text-[12px] leading-relaxed text-slate-500">
        Ingat kata sandimu? <a href="{{ route('login') }}"
            class="font-bold text-[#0A66C8] underline-offset-4 transition hover:underline">Masuk</a>
    </p>
</x-guest-layout>
