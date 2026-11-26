{{-- resources/views/dashboard/admin.blade.php --}}
<x-app-layout>
    @php
        $hasSessionTrend = array_sum($charts['sessionTrend']) > 0;
        $hasAttendance = array_sum(array_values($charts['attendance'])) > 0;
        $hasClassScores = count($charts['classScores']['labels']) > 0;
        $hasClassStudents = array_sum($charts['classStudents']['data']) > 0;
    @endphp

    <div class="space-y-4 sm:space-y-5">
        <section data-aos="fade-up"
            class="relative isolate overflow-hidden rounded-[26px] bg-[linear-gradient(128deg,#0B5FC0_0%,#1487E6_46%,#6ACBFA_100%)] px-5 py-7 text-white shadow-[0_30px_70px_-45px_rgba(6,60,120,.9)] sm:rounded-[30px] sm:px-8 sm:py-9">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -left-20 top-0 h-64 w-64 rounded-full bg-white/15 blur-3xl"></div>
                <div class="absolute -right-10 bottom-0 h-72 w-72 rounded-full bg-[#C8F24D]/25 blur-3xl"></div>
                <div class="absolute right-10 top-6 h-24 w-24 rounded-full border border-white/15"></div>
                <div class="absolute right-24 top-20 h-10 w-10 rounded-full border border-white/20"></div>
                <div
                    class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/40 to-transparent">
                </div>
            </div>

            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-xl">
                    <p
                        class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1.5 text-[9px] font-bold uppercase tracking-[0.2em] text-white/90 backdrop-blur">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                        Dashboard Administrator
                    </p>
                    <h1
                        class="mt-4 text-balance text-[clamp(1.7rem,3.4vw,2.5rem)] font-bold leading-[1.06] tracking-[-0.035em]">
                        Selamat datang, {{ explode(' ', trim($user->name))[0] }}
                    </h1>
                    <p class="mt-2.5 max-w-md text-[13px] leading-relaxed text-white/80">
                        Pantau kelas, pengguna, dan sesi praktikum dari satu tempat. Semua data operasional lab ada di
                        sini.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-2.5">
                        <a href="{{ route('sessions.index') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-[#C8F24D] px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0B0B0C] transition hover:bg-[#DAFF73]">
                            Kelola Sesi
                            <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                <path d="M5 12h14m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        <a href="{{ route('admin.master.students') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-white backdrop-blur transition hover:bg-white/20">
                            Kelola Pengguna
                        </a>
                    </div>
                </div>

                <div
                    class="flex w-full items-center justify-between gap-4 rounded-2xl border border-white/25 bg-white/10 px-4 py-3.5 backdrop-blur sm:w-auto sm:min-w-[15rem] sm:flex-col sm:items-start">
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-[0.18em] text-white/75">Hari ini</p>
                        <p class="mt-1 text-[13px] font-semibold">{{ now()->locale('id')->translatedFormat('l, d M Y') }}</p>
                    </div>
                    <div class="h-8 w-px bg-white/20 sm:h-px sm:w-full"></div>
                    <div class="flex items-center gap-2 text-[10px] font-semibold text-white/80">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#C8F24D] opacity-60"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-[#C8F24D]"></span>
                        </span>
                        {{ $stats['active_sessions'] }} sesi sedang aktif
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $cards = [
                    [
                        'label' => 'Total Kelas',
                        'value' => $stats['total_classes'],
                        'note' => 'Kelas praktikum terdaftar',
                        'tone' => 'bg-[#EAF4FF] text-[#0A66C8]',
                        'icon' => 'building',
                        'delay' => 0,
                    ],
                    [
                        'label' => 'Total Mahasiswa',
                        'value' => $stats['total_students'],
                        'note' => 'Akun mahasiswa aktif',
                        'tone' => 'bg-emerald-50 text-emerald-700',
                        'icon' => 'users',
                        'delay' => 60,
                    ],
                    [
                        'label' => 'Total Asisten',
                        'value' => $stats['total_assistants'],
                        'note' => 'Asisten pengampu kelas',
                        'tone' => 'bg-[#F1EEFF] text-indigo-700',
                        'icon' => 'badge',
                        'delay' => 120,
                    ],
                    [
                        'label' => 'Sesi Aktif',
                        'value' => $stats['active_sessions'],
                        'note' => 'QR presensi berjalan',
                        'tone' => 'bg-[#F7FFD9] text-[#3F5200]',
                        'icon' => 'calendar',
                        'delay' => 180,
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div data-aos="fade-up" data-aos-delay="{{ $card['delay'] }}"
                    class="group rounded-2xl border border-black/5 bg-white p-4 transition duration-300 hover:-translate-y-0.5 hover:border-black/10 hover:shadow-[0_18px_40px_-32px_rgba(11,11,12,.45)] sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">
                            {{ $card['label'] }}</p>
                        <span class="grid h-9 w-9 place-items-center rounded-xl {{ $card['tone'] }}">
                            <x-nav-icon name="{{ $card['icon'] }}" class="h-4 w-4" />
                        </span>
                    </div>
                    <p class="mt-3 text-[2rem] font-extrabold leading-none tracking-[-0.04em]">
                        {{ $card['value'] }}</p>
                    <p class="mt-1.5 text-[11px] text-slate-500">{{ $card['note'] }}</p>
                </div>
            @endforeach
        </div>

        <section class="space-y-3">
            <div class="flex flex-wrap items-end justify-between gap-3 px-1">
                <div>
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Statistik Web</p>
                    <h2 class="mt-1 text-lg font-bold tracking-[-0.02em]">Monitoring Praktikum</h2>
                </div>
                <span class="chip chip-sky">6 bulan terakhir</span>
            </div>

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.35fr_1fr]">
                <article data-aos="fade-up" class="card p-5 sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Tren</p>
                            <h3 class="mt-1 text-[15px] font-bold tracking-[-0.02em]">Sesi per Bulan</h3>
                        </div>
                        <div class="flex items-center gap-3 text-[10px] font-semibold text-slate-500">
                            <span class="inline-flex items-center gap-1.5"><span
                                    class="h-2 w-2 rounded-full bg-[#0A66C8]"></span>Total</span>
                            <span class="inline-flex items-center gap-1.5"><span
                                    class="h-2 w-2 rounded-full bg-[#0B0B0C]"></span>Selesai</span>
                        </div>
                    </div>
                    <div class="chart-box mt-4 h-[17rem]">
                        @if ($hasSessionTrend)
                            <canvas id="admin-session-chart" aria-label="Grafik tren sesi per bulan" role="img"></canvas>
                        @else
                            <x-chart-empty icon="calendar" title="Belum ada sesi"
                                description="Tren sesi muncul setelah pertemuan praktikum dibuat." />
                        @endif
                    </div>
                </article>

                <article data-aos="fade-up" data-aos-delay="80" class="card p-5 sm:p-6">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Presensi</p>
                    <h3 class="mt-1 text-[15px] font-bold tracking-[-0.02em]">Distribusi Kehadiran</h3>
                    <div class="chart-box mt-4 h-[17rem]">
                        @if ($hasAttendance)
                            <canvas id="admin-attendance-chart" aria-label="Grafik distribusi kehadiran"
                                role="img"></canvas>
                        @else
                            <x-chart-empty icon="qr" title="Belum ada presensi"
                                description="Distribusi kehadiran muncul setelah mahasiswa memindai QR." />
                        @endif
                    </div>
                </article>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <article data-aos="fade-up" class="card p-5 sm:p-6">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Akademik</p>
                    <h3 class="mt-1 text-[15px] font-bold tracking-[-0.02em]">Rata-Rata Nilai per Kelas</h3>
                    <div class="chart-box mt-4">
                        @if ($hasClassScores)
                            <canvas id="admin-class-score-chart" aria-label="Grafik rata-rata nilai per kelas"
                                role="img"></canvas>
                        @else
                            <x-chart-empty icon="chart" title="Belum ada nilai"
                                description="Rata-rata nilai per kelas tampil setelah nilai mahasiswa diisi." />
                        @endif
                    </div>
                </article>

                <article data-aos="fade-up" data-aos-delay="80" class="card p-5 sm:p-6">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Peserta</p>
                    <h3 class="mt-1 text-[15px] font-bold tracking-[-0.02em]">Mahasiswa per Kelas</h3>
                    <div class="chart-box mt-4">
                        @if ($hasClassStudents)
                            <canvas id="admin-class-student-chart" aria-label="Grafik jumlah mahasiswa per kelas"
                                role="img"></canvas>
                        @else
                            <x-chart-empty icon="users" title="Belum ada mahasiswa"
                                description="Jumlah mahasiswa per kelas tampil setelah akun ditempatkan." />
                        @endif
                    </div>
                </article>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.35fr_1fr]">
            <section data-aos="fade-up"
                class="rounded-[26px] border border-black/5 bg-white p-5 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Informasi</p>
                        <h2 class="mt-1 text-lg font-bold tracking-[-0.02em]">Pemberitahuan Terbaru</h2>
                    </div>
                    <a href="{{ route('admin.announcements.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full border border-black/5 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500 transition hover:bg-[#F5F5F2] hover:text-[#0B0B0C]">
                        Kelola
                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" aria-hidden="true">
                            <path d="M5 12h14m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>

                <div class="mt-4 space-y-2.5">
                    @forelse ($announcements as $info)
                        <article
                            class="rounded-2xl border border-black/5 bg-[#F9F9F6] p-4 transition hover:border-black/10 hover:bg-white">
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="rounded-full px-2.5 py-1 text-[8px] font-extrabold uppercase tracking-[0.14em] {{ $info->type === 'tugas' ? 'bg-amber-100 text-amber-800' : ($info->type === 'jadwal' ? 'bg-[#F1EEFF] text-indigo-700' : 'bg-[#EAF4FF] text-[#0A66C8]') }}">
                                    {{ $info->type }}
                                </span>
                                <span class="text-[10px] text-slate-500">{{ $info->created_at->locale('id')->diffForHumans() }}</span>
                                @unless ($info->is_published)
                                    <span class="text-[8px] font-bold uppercase tracking-wider text-slate-500">Draft</span>
                                @endunless
                            </div>
                            <h3 class="mt-2 text-[13px] font-bold leading-snug">{{ $info->title }}</h3>
                            <p class="mt-1 line-clamp-2 text-[12px] leading-relaxed text-slate-500">{{ $info->content }}
                            </p>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-black/10 px-4 py-10 text-center">
                            <p class="text-[13px] font-semibold text-slate-500">Belum ada pemberitahuan</p>
                            <p class="mt-1 text-[11px] text-slate-500">Buat info tugas atau jadwal untuk mahasiswa.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section data-aos="fade-up" data-aos-delay="100"
                class="rounded-[26px] border border-black/5 bg-white p-5 sm:p-6">
                <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Jalan Pintas</p>
                <h2 class="mt-1 text-lg font-bold tracking-[-0.02em]">Aksi Cepat</h2>

                <div class="mt-4 space-y-2">
                    @php
                        $links = [
                            ['label' => 'Upload tugas mingguan', 'href' => route('admin.assignments.index'), 'icon' => 'upload'],
                            ['label' => 'Buat pemberitahuan', 'href' => route('admin.announcements.index'), 'icon' => 'megaphone'],
                            ['label' => 'Atur kelas praktikum', 'href' => route('admin.classrooms.index'), 'icon' => 'building'],
                            ['label' => 'Export nilai Excel', 'href' => route('admin.grades.index'), 'icon' => 'table'],
                        ];
                    @endphp

                    @foreach ($links as $link)
                        <a href="{{ $link['href'] }}"
                            class="group flex items-center gap-3 rounded-xl border border-transparent px-3 py-2.5 text-[12.5px] font-semibold text-slate-600 transition hover:border-black/5 hover:bg-[#F5F5F2] hover:text-[#0B0B0C]">
                            <span
                                class="grid h-8 w-8 place-items-center rounded-lg bg-[#F5F5F2] text-slate-500 transition group-hover:bg-[#0B0B0C] group-hover:text-[#C8F24D]">
                                <x-nav-icon name="{{ $link['icon'] }}" class="h-4 w-4" />
                            </span>
                            {{ $link['label'] }}
                            <svg viewBox="0 0 24 24" fill="none"
                                class="ml-auto h-3.5 w-3.5 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#0A66C8]"
                                aria-hidden="true">
                                <path d="M5 12h14m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (!window.Sipro || !window.Chart) return;

                const data = @js($charts);

                Sipro.chart('admin-session-chart', {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                                label: 'Total sesi',
                                data: data.sessionTrend,
                                borderColor: '#0A66C8',
                                backgroundColor: 'rgba(10,102,200,.12)',
                                fill: true,
                                tension: .35,
                                borderWidth: 2,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'Selesai',
                                data: data.finishedTrend,
                                borderColor: '#0B0B0C',
                                backgroundColor: 'transparent',
                                tension: .35,
                                borderWidth: 2,
                                borderDash: [4, 4],
                                pointRadius: 0,
                                pointHoverRadius: 4,
                            },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
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

                Sipro.chart('admin-attendance-chart', {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                        datasets: [{
                            data: [data.attendance.hadir, data.attendance.izin, data.attendance.sakit, data
                                .attendance.alpha
                            ],
                            backgroundColor: ['#10B981', '#0A66C8', '#F59E0B', '#F43F5E'],
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

                Sipro.chart('admin-class-score-chart', {
                    type: 'bar',
                    data: {
                        labels: data.classScores.labels,
                        datasets: [{
                            label: 'Rata-rata nilai',
                            data: data.classScores.data,
                            backgroundColor: '#0A66C8',
                            hoverBackgroundColor: '#0B5FC0',
                            borderRadius: 8,
                            maxBarThickness: 40,
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
                                max: 100,
                                grid: {
                                    color: 'rgba(11,11,12,.06)'
                                }
                            },
                        },
                    },
                });

                Sipro.chart('admin-class-student-chart', {
                    type: 'bar',
                    data: {
                        labels: data.classStudents.labels,
                        datasets: [{
                            label: 'Mahasiswa',
                            data: data.classStudents.data,
                            backgroundColor: '#C8F24D',
                            hoverBackgroundColor: '#DAFF73',
                            borderRadius: 8,
                            maxBarThickness: 40,
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
