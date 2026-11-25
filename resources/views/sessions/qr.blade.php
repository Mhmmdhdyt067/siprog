<x-app-layout>
    @php
        $present = $attendees->where('status', 'hadir')->count();
        $percent = $totalStudents > 0 ? min(100, round(($present / $totalStudents) * 100)) : 0;
        $isActive = $session->status === 'active';
    @endphp

    <div class="mx-auto max-w-6xl space-y-4">
        <x-page-header eyebrow="Mode Proyektor" :title="'Pertemuan ' . $session->meeting_number . ': ' . $session->title"
            description="Tampilkan QR presensi di layar kelas. Kehadiran mahasiswa masuk secara langsung ke panel sebelah.">
            <x-slot:meta>
                <span class="chip {{ $isActive ? 'chip-lime' : 'chip-emerald' }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $isActive ? 'bg-[#C8F24D]' : 'bg-emerald-500' }}"></span>
                    {{ $isActive ? 'Sesi Aktif' : 'Sesi Selesai' }}
                </span>
                <span class="chip chip-sky">{{ $session->classroom?->name }}</span>
                <span class="chip">{{ $session->session_date?->locale('id')->translatedFormat('l, d M Y') }}</span>
                <span class="chip">Token s.d. <span
                        id="qr-expires">{{ $session->qr_expires_at?->format('H:i') ?? '—' }}</span></span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('sessions.index') }}" class="btn btn-light">
                    <x-nav-icon name="arrow-left" class="h-3.5 w-3.5" />
                    Daftar Sesi
                </a>
                <button type="button" id="qr-refresh" class="btn btn-light">
                    <x-nav-icon name="refresh" class="h-3.5 w-3.5" />
                    Perbarui QR
                </button>
                <button type="button" id="finish-session" class="btn btn-danger">
                    <x-nav-icon name="check" class="h-3.5 w-3.5" />
                    Akhiri Sesi
                </button>
            </x-slot:actions>
        </x-page-header>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,.9fr)]">
            <section class="card relative overflow-hidden px-5 py-6 text-center sm:px-8 sm:py-8">
                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                    <div class="absolute -left-16 -top-20 h-56 w-56 rounded-full bg-[#EAF4FF] blur-3xl"></div>
                    <div class="absolute -bottom-24 right-0 h-48 w-48 rounded-full bg-[#F7FFD9] blur-3xl"></div>
                </div>

                <div class="relative">
                    <p class="inline-flex items-center gap-2 rounded-full border border-black/5 bg-white px-3 py-1.5 text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#0A66C8] opacity-50"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-[#0A66C8]"></span>
                        </span>
                        Scan Presensi Mahasiswa
                    </p>

                    <div id="qr-panel"
                        class="mx-auto mt-5 w-full max-w-[17rem] rounded-[26px] border border-black/10 bg-white p-4 shadow-[0_24px_60px_-34px_rgba(11,11,12,.35)] [&>svg]:block [&>svg]:h-auto [&>svg]:w-full sm:max-w-[19rem] sm:p-5">
                        {!! $qrSvg !!}
                    </div>

                    <p class="mx-auto mt-4 max-w-sm text-[12px] leading-relaxed text-slate-500">
                        Mahasiswa membuka menu <strong class="text-[#0B0B0C]">Presensi QR</strong> lalu mengarahkan
                        kamera ke kode di atas.
                    </p>

                    <div class="mt-6 rounded-2xl border border-black/5 bg-[#F9F9F6] px-4 py-4 text-left">
                        <div class="flex items-end justify-between gap-3">
                            <div>
                                <p class="text-[9px] font-extrabold uppercase tracking-[0.16em] text-slate-500">Sudah
                                    hadir</p>
                                <p class="mt-1 text-[1.8rem] font-extrabold leading-none tracking-[-0.04em]">
                                    <span id="present-count">{{ $present }}</span>
                                    <span class="text-[13px] font-semibold text-slate-400">/ {{ $totalStudents }}</span>
                                </p>
                            </div>
                            <span class="text-[11px] font-bold text-[#0A66C8]"><span
                                    id="attendance-percent">{{ $percent }}</span>%</span>
                        </div>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-black/5">
                            <div id="attendance-progress"
                                class="h-full rounded-full bg-[linear-gradient(90deg,#0A66C8,#6ACBFA)] transition-[width] duration-500"
                                style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card flex min-h-[26rem] flex-col px-4 py-4 sm:px-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Live</p>
                        <h2 class="mt-1 text-[16px] font-bold tracking-[-0.02em]">Kehadiran Masuk</h2>
                    </div>
                    <span class="chip chip-sky">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#0A66C8] opacity-60"></span>
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-[#0A66C8]"></span>
                        </span>
                        Auto-refresh
                    </span>
                </div>

                <div id="attendees-panel" class="mt-4 min-h-0 flex-1 overflow-y-auto pr-1">
                    @include('sessions.partials.attendees', ['attendees' => $attendees])
                </div>

                <p class="mt-3 border-t border-black/5 pt-3 text-[10px] text-slate-500">
                    Diperbarui <span id="attendance-updated">{{ now()->format('H:i:s') }}</span> · otomatis tiap 8 detik
                </p>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const attendeesUrl = @js(route('sessions.attendees', $session));
                const totalStudents = {{ (int) $totalStudents }};
                let polling = false;

                const setProgress = function(present) {
                    const percent = totalStudents > 0 ? Math.min(100, Math.round((present / totalStudents) * 100)) : 0;
                    $('#present-count').text(present);
                    $('#attendance-percent').text(percent);
                    $('#attendance-progress').css('width', percent + '%');
                };

                const poll = function() {
                    if (polling || document.hidden) return;
                    polling = true;

                    $.getJSON(attendeesUrl)
                        .done(function(response) {
                            $('#attendees-panel').html(response.html);
                            setProgress(Number(response.present) || 0);
                            $('#attendance-updated').text(response.updated_at || '');
                        })
                        .always(function() {
                            polling = false;
                        });
                };

                window.setInterval(poll, 8000);

                let rotating = false;
                let rotateTimer = null;

                const scheduleRotate = function(seconds) {
                    window.clearTimeout(rotateTimer);
                    // Cek ringan tiap maksimal 8 detik supaya tab lain cepat ikut
                    // token terbaru; server tetap menolak rotasi < 25 detik.
                    const delay = Math.min(Math.max(1, Number(seconds) || 8), 8);
                    rotateTimer = window.setTimeout(function() {
                        if (document.hidden) {
                            scheduleRotate(5);
                            return;
                        }
                        rotateQr(true);
                    }, delay * 1000);
                };

                const rotateQr = function(auto) {
                    if (rotating) {
                        if (auto) scheduleRotate(2);
                        return;
                    }
                    rotating = true;

                    const button = $('#qr-refresh');
                    if (!auto) button.prop('disabled', true);

                    // Tab ganda aman: rotasi otomatis dibatasi server, tab lain
                    // memakai token yang sama. Tombol manual memaksa rotasi.
                    const url = @js(route('sessions.qr.refresh', $session)) + (auto ? '' : '?force=1');

                    Sipro.post(url)
                        .done(function(response) {
                            $('#qr-panel').html(response.qr_svg);
                            $('#qr-expires').text(response.expires_at || '—');
                            if (!auto) Sipro.toast(response.message || 'Token QR berhasil diperbarui.');
                            if (auto) scheduleRotate(response.next_rotate_in);
                        })
                        .fail(function(xhr) {
                            if (!auto) Sipro.toast(xhr.responseJSON?.message || 'Gagal memperbarui token QR.', 'error');
                            if (auto) scheduleRotate(10);
                        })
                        .always(function() {
                            rotating = false;
                            if (!auto) button.prop('disabled', false);
                        });
                };

                $('#qr-refresh').on('click', function() {
                    rotateQr(false);
                });

                // QR berganti tiap ~26 detik; hanya token terbaru yang valid.
                // Jadwal mengikuti sisa waktu dari server supaya semua tab sinkron.
                scheduleRotate(1);

                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden) {
                        window.clearTimeout(rotateTimer);
                        rotateQr(true);
                    }
                });

                $('#finish-session').on('click', function() {
                    Sipro.confirm({
                        title: 'Akhiri sesi praktikum?',
                        message: 'Presensi ditutup dan mahasiswa dapat mengisi feedback. Nilai tetap bisa diisi setelahnya.',
                        confirmText: 'Ya, akhiri sesi',
                        tone: 'danger',
                        onConfirm: function() {
                            return Sipro.post(@js(route('sessions.finish', $session)))
                                .done(function(response) {
                                    Sipro.toast(response.message || 'Sesi praktikum selesai.');
                                    window.location.href = response.redirect || @js(route('sessions.index'));
                                })
                                .fail(function(xhr) {
                                    Sipro.toast(xhr.responseJSON?.message || 'Gagal mengakhiri sesi.', 'error');
                                });
                        },
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
