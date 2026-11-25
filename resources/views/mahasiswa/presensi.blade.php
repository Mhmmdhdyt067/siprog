<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="p-4 sm:p-6 rounded-[22px] bg-white border border-black/5 text-center" data-aos="fade-up">
            <h1 class="text-2xl font-bold mb-2">Presensi QR Code</h1>
            <p class="text-sm text-slate-500 mb-6">Arahkan kamera ke QR Code yang ditampilkan asisten di proyektor.</p>

            <div id="qr-scan" data-scan-url="{{ route('mahasiswa.presensi.scan') }}" data-csrf="{{ csrf_token() }}">
                <div class="relative mx-auto w-full max-w-md overflow-hidden rounded-[22px] border-2 border-sky-300 bg-[#0B0B0C]">
                    <div id="qr-reader" class="min-h-[280px]"></div>

                    <div id="qr-idle" class="absolute inset-0 flex flex-col items-center justify-center gap-4 px-6 text-center">
                        <svg class="h-12 w-12 text-white/35" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
                        </svg>
                        <p id="qr-idle-text" class="text-xs text-white/60">Kamera belum aktif.</p>
                        <button type="button" id="qr-start" class="btn btn-lime">Nyalakan Kamera</button>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <button type="button" data-mode="camera" aria-pressed="true"
                        class="btn btn-primary">Scan via Kamera</button>
                    <button type="button" data-mode="upload" aria-pressed="false"
                        class="btn btn-light">Upload Gambar</button>
                </div>

                <div id="qr-upload-panel" class="mt-4 hidden space-y-2">
                    <label for="qr-file" class="btn btn-primary cursor-pointer">
                        Pilih Gambar QR
                    </label>
                    <input id="qr-file" type="file" accept="image/*" class="sr-only">
                    <p class="text-xs text-slate-500">Unggah tangkapan layar atau foto QR yang ditampilkan asisten.</p>
                </div>

                <p id="qr-status" role="status" class="mt-3 text-sm text-slate-500"></p>
            </div>
        </div>

        <!-- Riwayat Kehadiran -->
        <div class="p-4 sm:p-6 rounded-[22px] bg-white border border-black/5" data-aos="fade-up">
            <h2 class="text-lg font-bold mb-4">Riwayat Kehadiran Anda</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-slate-500 border-b border-black/5">
                        <tr>
                            <th class="py-3">Pertemuan</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 hidden sm:table-cell">Waktu Scan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @forelse ($history as $att)
                            <tr>
                                <td class="py-3 pr-2">Pertemuan {{ $att->session->meeting_number }} —
                                    {{ $att->session->title }}</td>
                                <td class="py-3"><span
                                        class="px-2 py-1 rounded bg-emerald-50 text-emerald-600 text-xs uppercase">{{ $att->status }}</span>
                                </td>
                                <td class="py-3 text-slate-500 hidden sm:table-cell">
                                    {{ $att->scanned_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-500">Belum ada riwayat kehadiran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Pilihan Setelah Scan Berhasil -->
    <div id="pretest-modal" class="hidden fixed inset-0 z-50 bg-black/75 flex items-center justify-center p-4">
        <div class="bg-white border border-sky-300 rounded-[22px] p-6 sm:p-8 max-w-md w-full max-h-full overflow-y-auto text-center">
            <h3 class="text-2xl font-bold text-emerald-600">Presensi Berhasil!</h3>
            <p id="modal-session-title" class="text-slate-600 mt-2 text-sm"></p>
            <p class="text-slate-500 text-xs mt-2">Pilih langkah selanjutnya:</p>
            <div class="mt-6 flex flex-col gap-3">
                <a id="btn-go-pretest" href="#"
                    class="hidden w-full py-3 rounded-xl bg-sky-500 hover:bg-sky-600 font-bold">
                    Lanjut ke Pre-Test (15 Menit)
                </a>
                <a id="btn-go-dashboard" href="{{ route('dashboard') }}"
                    class="w-full py-3 rounded-xl bg-[#F5F5F2] hover:bg-slate-200 font-semibold">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    @vite('resources/js/presensi.js')
</x-app-layout>
