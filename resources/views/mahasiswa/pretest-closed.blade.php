<x-app-layout>
    <div class="max-w-xl mx-auto py-20 px-4 text-center">
        <div class="rounded-[26px] border border-emerald-200 bg-white p-8 text-[#0B0B0C] shadow-[0_30px_80px_-50px_rgba(11,11,12,.5)] sm:p-10" data-aos="zoom-in">
            <div
                class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 text-3xl mb-4">
                ✓
            </div>
            <h1 class="text-2xl font-bold text-emerald-600">Pre-Test Telah Ditutup</h1>
            <p class="text-slate-600 mt-3 text-lg">
                Selamat menyimak proses praktikum!
            </p>
            <p class="text-sm text-slate-500 mt-2">
                Silakan kumpulkan lembar jawaban fisik Anda kepada asisten, lalu ikuti arahan praktikum secara langsung.
            </p>
            <a href="{{ route('dashboard') }}"
                class="inline-block mt-6 px-6 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 font-semibold">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</x-app-layout>
