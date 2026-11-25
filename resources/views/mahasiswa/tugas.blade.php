<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold">Tugas Mingguan Praktikum</h1>
        <div class="space-y-4">
            @forelse($assignments as $task)
                @php($submission = $task->submissions->first())
                <div class="p-6 rounded-[22px] bg-white border border-black/5 space-y-4" data-aos="fade-up">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div class="min-w-0 flex-1">
                            <span class="text-xs text-[#0A66C8] font-semibold">Pertemuan
                                {{ $task->session->meeting_number }}</span>
                            <h3 class="text-xl font-bold break-words">{{ $task->title }}</h3>
                            <p class="text-sm text-slate-500 mt-1 break-words">{{ $task->description }}</p>
                            <p class="text-xs text-amber-600 mt-2">Batas Waktu:
                                {{ $task->deadline->format('d M Y, H:i') }} WIB</p>
                        </div>
                        <a href="{{ route('assignments.file', $task) }}"
                            class="px-4 py-2 rounded-xl bg-[#F5F5F2] hover:bg-slate-200 border border-black/10 text-sm font-semibold">
                            Unduh Soal Tugas
                        </a>
                    </div>

                    <div class="pt-4 border-t border-black/5">
                        @if ($submission)
                            <p class="text-xs text-emerald-600 mb-3 break-all">
                                ✓ Sudah diunggah: <strong>{{ $submission->original_filename }}</strong>
                                ({{ $submission->submitted_at->format('d M Y H:i') }})
                            </p>
                        @endif

                        <form action="{{ route('mahasiswa.tugas.submit', $task) }}" method="POST"
                            enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                            @csrf
                            <input type="file" name="file_pdf" accept="application/pdf" required
                                class="w-full min-w-0 max-w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#EAF4FF] file:text-[#0A66C8] hover:file:bg-sky-500/30 sm:w-auto">
                            <button type="submit"
                                class="w-full px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-sm font-semibold sm:w-auto">
                                {{ $submission ? 'Unggah Ulang PDF' : 'Upload Scan PDF' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 rounded-[22px] bg-white border border-black/5 text-center text-slate-500">
                    Belum ada tugas mingguan yang dirilis.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
