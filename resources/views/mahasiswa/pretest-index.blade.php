<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold">Menu Pre-Test Praktikum</h1>
        <div class="grid gap-4">
            @foreach ($sessions as $sess)
                @php($att = $sess->attendances->first())
                <div class="p-4 sm:p-6 rounded-[22px] bg-white border border-black/5 flex flex-wrap justify-between items-center gap-4"
                    data-aos="fade-up">
                    <div class="min-w-0">
                        <h3 class="font-bold text-lg break-words">Pertemuan {{ $sess->meeting_number }}: {{ $sess->title }}</h3>
                        <p class="text-xs text-slate-500">Durasi pengerjaan: 15 Menit (Jawaban ditulis di kertas &
                            dikumpul fisik)</p>
                    </div>
                    <div>
                        @if (!$att)
                            <span class="px-3 py-1.5 rounded-lg bg-[#F5F5F2] text-slate-500 text-xs">Belum
                                Presensi</span>
                        @elseif($att->pretest_finished_at)
                            <span
                                class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-semibold">Sudah
                                Selesai / Tertutup</span>
                        @elseif($att->pretest_locked_at)
                            <span class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-semibold">
                                Dikunci karena pelanggaran
                            </span>
                        @else
                            <a href="{{ route('mahasiswa.pretest.show', $sess) }}"
                                class="px-5 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 font-semibold text-sm">
                                Buka Soal Pre-Test
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
