<x-guest-layout>
    <div class="mb-8">
        <p class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.22em] text-slate-500">
            <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
            Reset kata sandi
        </p>
        <h2 class="mt-3 text-balance text-[clamp(1.8rem,3.2vw,2.35rem)] font-semibold leading-[1.08] tracking-[-0.04em]">
            Buat kata sandi baru</h2>
        <p class="mt-2 text-[13px] leading-relaxed text-slate-500">Pilih kata sandi baru untuk akunmu. Gunakan minimal
            8 karakter.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2" type="email" name="email" :value="old('email', $request->email)" required
                autofocus autocomplete="username" placeholder="nama@kampus.ac.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password" value="Kata sandi baru" />
            <x-text-input id="password" class="mt-2" type="password" name="password" required
                autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi kata sandi" />
            <x-text-input id="password_confirmation" class="mt-2" type="password" name="password_confirmation" required
                autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <x-primary-button>Simpan kata sandi</x-primary-button>
    </form>

    <p class="mt-7 text-center text-[12px] leading-relaxed text-slate-500">
        Sudah ingat? <a href="{{ route('login') }}"
            class="font-bold text-[#0A66C8] underline-offset-4 transition hover:underline">Kembali ke halaman masuk</a>
    </p>
</x-guest-layout>
