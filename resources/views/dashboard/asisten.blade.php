{{-- resources/views/dashboard/asisten.blade.php --}}
<x-app-layout>
    @php
        $hasSessions = count($charts['sessionLabels']) > 0;
        $hasGrades = array_sum($charts['scoreBuckets']['data']) > 0;
        $hasSubmissions = array_sum($charts['submissions']) > 0;
    @endphp

    <div class="space-y-4 sm:space-y-5">
        <section data-aos="fade-up"
            class="relative isolate overflow-hidden rounded-[26px] bg-[linear-gradient(128deg,#0B5FC0_0%,#1487E6_46%,#6ACBFA_100%)] px-5 py-7 text-white shadow-[0_30px_70px_-45px_rgba(6,60,120,.9)] sm:rounded-[30px] sm:px-8 sm:py-9">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -left-20 top-0 h-64 w-64 rounded-full bg-white/15 blur-3xl"></div>
                <div class="absolute -right-10 bottom-0 h-72 w-72 rounded-full bg-[#C8F24D]/25 blur-3xl"></div>
                <div class="absolute right-10 top-6 h-24 w-24 rounded-full border border-white/15"></div>
                <div class="absolute right-24 top-20 h-10 w-10 rounded-full border border-white/20"></div>
            </div>

            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-xl">
                    <p
                        class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1.5 text-[9px] font-bold uppercase tracking-[0.2em] text-white/90 backdrop-blur">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        Dashboard Asisten
                    </p>
                    <h1
                        class="mt-4 text-balance text-[clamp(1.7rem,3.4vw,2.5rem)] font-bold leading-[1.06] tracking-[-0.035em]">
                        Selamat datang, {{ explode(' ', trim($user->name))[0] }}
                    </h1>
                    <p class="mt-2.5 max-w-md text-[13px] leading-relaxed text-white/80">
                        Kelola sesi praktikum, tampilkan QR presensi, dan tinjau tugas mahasiswa yang masuk.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-2.5">
                        <a href="{{ route('sessions.index') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-[#C8F24D] px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0B0B0C] transition hover:bg-[#DAFF73]">
                            Buat / Kelola Sesi
                            <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                <path d="M5 12h14m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <a href="{{ route('profile.edit') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-white backdrop-blur transition hover:bg-white/20">
                            Profil Saya
                        </a>
                    </div>
                </div>

                <div class="grid w-full grid-cols-2 gap-2.5 sm:w-auto">
                    <div class="rounded-2xl border border-white/25 bg-white/10 px-4 py-3.5 backdrop-blur">
                        <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-white/75">Sesi Aktif</p>
                        <p class="mt-1 text-2xl font-extrabold leading-none">{{ $activeSessions->count() }}</p>
                        <p class="mt-1 text-[10px] text-white/80">siap diproyeksikan</p>
                    </div>
                    <div class="rounded-2xl border border-white/25 bg-white/10 px-4 py-3.5 backdrop-blur">
                        <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-white/75">Tugas Masuk</p>
                        <p class="mt-1 text-2xl font-extrabold leading-none">{{ $recentSubmissions->count() }}</p>
                        <p class="mt-1 text-[10px] text-white/80">pengumpulan terbaru</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <div class="flex flex-wrap items-end justify-between gap-3 px-1">
                <div>
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Statistik Web</p>
                    <h2 class="mt-1 text-lg font-bold tracking-[-0.02em]">Monitoring Kelas Anda</h2>
                </div>
                <span class="chip chip-sky">6 sesi terakhir</span>
            </div>

            <article data-aos="fade-up" class="card p-5 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Presensi</p>
                        <h3 class="mt-1 text-[15px] font-bold tracking-[-0.02em]">Kehadiran per Sesi</h3>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-1.5"><span
                                class="h-2 w-2 rounded-full bg-[#10B981]"></span>Hadir</span>
                        <span class="inline-flex items-center gap-1.5"><span
                                class="h-2 w-2 rounded-full bg-[#F43F5E]"></span>Tidak hadir</span>
                    </div>
                </div>
                <div class="chart-box mt-4 h-[17rem]">
                    @if ($hasSessions)
                        <canvas id="assistant-attendance-chart" aria-label="Grafik kehadiran per sesi" role="img"></canvas>
                    @else
                        <x-chart-empty icon="calendar" title="Belum ada sesi"
                            description="Grafik kehadiran muncul setelah kelas Anda memiliki sesi praktikum." />
                    @endif
                </div>
            </article>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <article data-aos="fade-up" class="card p-5 sm:p-6">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Akademik</p>
                    <h3 class="mt-1 text-[15px] font-bold tracking-[-0.02em]">Distribusi Nilai</h3>
                    <div class="chart-box mt-4">
                        @if ($hasGrades)
                            <canvas id="assistant-score-chart" aria-label="Grafik distribusi nilai" role="img"></canvas>
                        @else
                            <x-chart-empty icon="chart" title="Belum ada nilai"
                                description="Distribusi nilai tampil setelah mahasiswa dinilai pada sesi Anda." />
                        @endif
                    </div>
                </article>

                <article data-aos="fade-up" data-aos-delay="80" class="card p-5 sm:p-6">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Tugas</p>
                    <h3 class="mt-1 text-[15px] font-bold tracking-[-0.02em]">Pengumpulan per Sesi</h3>
                    <div class="chart-box mt-4">
                        @if ($hasSubmissions)
                            <canvas id="assistant-submission-chart" aria-label="Grafik pengumpulan tugas per sesi"
                                role="img"></canvas>
                        @else
                            <x-chart-empty icon="upload" title="Belum ada pengumpulan"
                                description="PDF tugas mahasiswa tampil di grafik ini setelah dikumpulkan." />
                        @endif
                    </div>
                </article>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <section data-aos="fade-up" class="rounded-[26px] border border-black/5 bg-white p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Berlangsung</p>
                        <h2 class="mt-1 text-lg font-bold tracking-[-0.02em]">Sesi Aktif Saat Ini</h2>
                    </div>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#F7FFD9] text-[#3F5200]">
                        <x-nav-icon name="qr" class="h-4 w-4" />
                    </span>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse ($activeSessions as $s)
                        <div
                            class="flex items-center justify-between gap-3 rounded-2xl border border-black/5 bg-[#F9F9F6] p-4">
                            <div class="min-w-0">
                                <p class="truncate text-[13px] font-bold">
                                    {{ $s->classroom->name }} — Pertemuan {{ $s->meeting_number }}
                                </p>
                                <p class="mt-0.5 truncate text-[11px] text-slate-500">{{ $s->title }}</p>
                            </div>
                            <a href="{{ route('sessions.qr', $s) }}"
                                class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-[#0B0B0C] px-3.5 py-2 text-[9px] font-extrabold uppercase tracking-[0.12em] text-white transition hover:bg-[#0A66C8]">
                                Proyektor QR
                            </a>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-black/10 px-4 py-9 text-center">
                            <p class="text-[13px] font-semibold text-slate-500">Tidak ada sesi aktif</p>
                            <p class="mt-1 text-[11px] text-slate-500">Mulai sesi baru agar QR presensi tampil.</p>
                            <a href="{{ route('sessions.index') }}"
                                class="mt-3 inline-flex text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0A66C8] hover:underline">
                                Buat sesi sekarang
                            </a>
                        </div>
                    @endforelse
                </div>
            </section>

            <section data-aos="fade-up" data-aos-delay="100"
                class="rounded-[26px] border border-black/5 bg-white p-5 sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Terbaru</p>
                        <h2 class="mt-1 text-lg font-bold tracking-[-0.02em]">Upload Tugas PDF</h2>
                    </div>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#EAF4FF] text-[#0A66C8]">
                        <x-nav-icon name="doc" class="h-4 w-4" />
                    </span>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse ($recentSubmissions as $sub)
                        <div
                            class="flex items-center gap-3 rounded-xl border border-black/5 bg-[#F9F9F6] px-3 py-2.5 transition hover:bg-white">
                            <span
                                class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-[#0B0B0C] text-[10px] font-extrabold uppercase text-white">
                                {{ mb_substr($sub->user->name, 0, 1) }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[12px] font-semibold">{{ $sub->user->name }}</p>
                                <p class="text-[10px] text-slate-500">
                                    Pertemuan {{ $sub->assignment->session->meeting_number }} ·
                                    {{ $sub->submitted_at?->locale('id')->diffForHumans() ?? 'baru saja' }}
                                </p>
                            </div>
                            <a href="{{ route('submissions.view', $sub) }}" target="_blank"
                                rel="noopener"
                                class="shrink-0 rounded-full border border-black/5 px-3 py-1.5 text-[9px] font-extrabold uppercase tracking-[0.1em] text-slate-600 transition hover:border-[#0A66C8]/20 hover:bg-[#EAF4FF] hover:text-[#0A66C8]">
                                Buka PDF
                            </a>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-black/10 px-4 py-9 text-center">
                            <p class="text-[13px] font-semibold text-slate-500">Belum ada pengumpulan tugas</p>
                            <p class="mt-1 text-[11px] text-slate-500">PDF mahasiswa akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (!window.Sipro || !window.Chart) return;

                const data = @js($charts);

                Sipro.chart('assistant-attendance-chart', {
                    type: 'bar',
                    data: {
                        labels: data.sessionLabels,
                        datasets: [{
                                label: 'Hadir',
                                data: data.present,
                                backgroundColor: '#10B981',
                                hoverBackgroundColor: '#059669',
                                borderRadius: 7,
                                maxBarThickness: 34,
                            },
                            {
                                label: 'Tidak hadir',
                                data: data.absent,
                                backgroundColor: 'rgba(244,63,94,.55)',
                                hoverBackgroundColor: '#F43F5E',
                                borderRadius: 7,
                                maxBarThickness: 34,
                            },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(11,11,12,.06)'
                                }
                            },
                        },
                    },
                });

                Sipro.chart('assistant-score-chart', {
                    type: 'doughnut',
                    data: {
                        labels: data.scoreBuckets.labels,
                        datasets: [{
                            data: data.scoreBuckets.data,
                            backgroundColor: ['#F43F5E', '#F59E0B', '#0A66C8', '#10B981'],
                            borderWidth: 0,
                            hoverOffset: 4,
                        }, ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutout: '64%',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                    },
                });

                Sipro.chart('assistant-submission-chart', {
                    type: 'bar',
                    data: {
                        labels: data.sessionLabels,
                        datasets: [{
                            label: 'Pengumpulan',
                            data: data.submissions,
                            backgroundColor: '#0A66C8',
                            hoverBackgroundColor: '#0B5FC0',
                            borderRadius: 7,
                            maxBarThickness: 34,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: 'rgba(11,11,12,.06)'
                                }
                            },
                        },
                    },
                });
            });
        </script>
    @endpush
</x-app-layout>
