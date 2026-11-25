<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Pre-Test Praktikum" title="Soal Pre-Test"
            description="Pilih sesi dari kelas yang Anda ampu untuk menyusun soal.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $sessions->total() }} sesi</span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('sessions.index') }}" class="btn btn-light">
                    <x-nav-icon name="arrow-left" class="h-3.5 w-3.5" />
                    Daftar Sesi
                </a>
            </x-slot:actions>
        </x-page-header>

        <div class="space-y-2.5">
            @forelse ($sessions as $session)
                <article class="card flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5"
                    data-aos="fade-up">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="chip chip-sky">{{ $session->classroom?->name }}</span>
                            <span class="chip">Pertemuan {{ $session->meeting_number }}</span>
                            <span
                                class="chip">{{ $session->session_date?->locale('id')->translatedFormat('d M Y') }}</span>
                        </div>
                        <h3 class="mt-2 text-[15px] font-bold leading-snug tracking-[-0.01em]">{{ $session->title }}</h3>
                        <p class="mt-1 text-[11px] font-semibold text-slate-500">
                            {{ $session->pretest_questions_count }} soal tersusun
                        </p>
                    </div>

                    <a href="{{ route('admin.pretest.index', $session) }}" class="btn btn-primary btn-sm shrink-0">
                        <x-nav-icon name="doc" class="h-3.5 w-3.5" />
                        Kelola Soal
                    </a>
                </article>
            @empty
                <div class="card px-5 py-10 text-center text-[12.5px] text-slate-500">
                    Belum ada sesi di kelas Anda.
                </div>
            @endforelse
        </div>

        {{ $sessions->links() }}
    </div>
</x-app-layout>
