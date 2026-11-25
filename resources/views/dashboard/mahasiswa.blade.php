<x-app-layout>
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
                        Dashboard Mahasiswa
                    </p>
                    <h1
                        class="mt-4 text-balance text-[clamp(1.7rem,3.4vw,2.5rem)] font-bold leading-[1.06] tracking-[-0.035em]">
                        Halo, {{ explode(' ', trim($user->name))[0] }}
                    </h1>
                    <p class="mt-2.5 max-w-md text-[13px] leading-relaxed text-white/80">
                        @if ($user->classroom)
                            Kelas <span class="font-semibold text-white">{{ $user->classroom->name }}</span> ·
                        @endif
                        Pantau kehadiran, pre-test, tugas, dan nilai praktikummu di satu tempat.
                    </p>

                    <div class="mt-5 max-w-sm">
                        <div class="flex items-center justify-between text-[9px] font-bold uppercase tracking-[0.16em] text-white/80">
                            <span>Kehadiran praktikum</span>
                            <span class="text-white">{{ $graduation['attendance_percentage'] }}%</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-white/25">
                            <span class="block h-full rounded-full bg-[#C8F24D]"
                                style="width: {{ min(100, (float) $graduation['attendance_percentage']) }}%"></span>
                        </div>
                        <p class="mt-1.5 text-[10px] text-white/80">
                            {{ $graduation['attended_count'] }} kehadiran tercatat ·
                            {{ $graduation['total_sessions'] }} pertemuan selesai
                        </p>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-2.5">
                        @if ($activeSession)
                            <a href="{{ route('mahasiswa.presensi.index') }}"
                                class="inline-flex items-center gap-2 rounded-full bg-[#C8F24D] px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0B0B0C] transition hover:bg-[#DAFF73]">
                                Scan QR Presensi
                                <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                    <path d="M5 12h14m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('mahasiswa.nilai.index') }}"
                                class="inline-flex items-center gap-2 rounded-full bg-[#C8F24D] px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0B0B0C] transition hover:bg-[#DAFF73]">
                                Lihat Nilai
                                <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                                    <path d="M5 12h14m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        @endif
                        <a href="{{ route('mahasiswa.tugas.index') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-white backdrop-blur transition hover:bg-white/20">
                            Tugas Mingguan
                        </a>
                    </div>
                </div>

                <div
                    class="w-full rounded-2xl border border-white/25 bg-white/10 px-4 py-4 backdrop-blur sm:w-auto sm:min-w-[13rem]">
                    <p class="text-[9px] font-bold uppercase tracking-[0.16em] text-white/75">Rata-rata nilai</p>
                    <div class="mt-1 flex flex-wrap items-end gap-2">
                        <p class="text-[2.4rem] font-extrabold leading-none tracking-[-0.04em]">
                            {{ $graduation['average_score'] }}</p>
                        <span
                            class="mb-1 max-w-full rounded-full px-2.5 py-1 text-[8px] font-extrabold uppercase leading-tight tracking-[0.12em] {{ $graduation['is_passed'] ? 'bg-[#C8F24D] text-[#0B0B0C]' : 'bg-rose-500 text-white' }}">
                            {{ $graduation['label'] }}
                        </span>
                    </div>
                    <p class="mt-2 text-[10px] text-white/80">Ambang lulus: kehadiran ≥ 75% · rata-rata ≥ 70.00</p>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div data-aos="fade-up"
                class="rounded-2xl border border-black/5 bg-white p-4 sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Kehadiran</p>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#EAF4FF] text-[#0A66C8]">
                        <x-nav-icon name="qr" class="h-4 w-4" />
                    </span>
                </div>
                <p class="mt-3 text-[2rem] font-extrabold leading-none tracking-[-0.04em]">
                    {{ $graduation['attendance_percentage'] }}%</p>
                <p class="mt-1.5 text-[11px] text-slate-500">{{ $graduation['attended_count'] }} kehadiran tercatat ·
                    {{ $graduation['total_sessions'] }} pertemuan selesai</p>
            </div>

            <div data-aos="fade-up" data-aos-delay="70"
                class="rounded-2xl border border-black/5 bg-white p-4 sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Rata-Rata Nilai</p>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
                        <x-nav-icon name="chart" class="h-4 w-4" />
                    </span>
                </div>
                <p class="mt-3 text-[2rem] font-extrabold leading-none tracking-[-0.04em]">
                    {{ $graduation['average_score'] }}</p>
                <p class="mt-1.5 text-[11px] text-slate-500">Dari seluruh pertemuan praktikum</p>
            </div>

            <div data-aos="fade-up" data-aos-delay="140"
                class="rounded-2xl border border-black/5 bg-white p-4 sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Status</p>
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#F7FFD9] text-[#3F5200]">
                        <x-nav-icon name="badge" class="h-4 w-4" />
                    </span>
                </div>
                <p class="mt-3 text-[1.3rem] sm:text-[1.4rem] xl:text-[1.55rem] font-extrabold leading-tight tracking-[-0.03em] break-words">
                    {{ $graduation['label'] }}</p>
                <p class="mt-1.5 text-[11px] text-slate-500">
                    {{ $graduation['is_passed'] ? 'Syarat kelulusan terpenuhi' : 'Syarat kelulusan belum terpenuhi' }}
                </p>
            </div>
        </div>

        @if ($activeSession)
            <section data-aos="fade-up"
                class="relative isolate overflow-hidden rounded-[26px] bg-[#0B0B0C] px-5 py-6 text-white sm:px-7">
                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                    <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-[#0A66C8]/30 blur-3xl"></div>
                    <div class="absolute -bottom-20 left-1/3 h-56 w-56 rounded-full bg-[#C8F24D]/15 blur-3xl"></div>
                </div>
                <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="inline-flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.2em] text-[#C8F24D]">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#C8F24D] opacity-70"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-[#C8F24D]"></span>
                            </span>
                            Sesi sedang berlangsung
                        </p>
                        <h2 class="mt-2 text-xl font-bold tracking-[-0.02em] sm:text-2xl">
                            Pertemuan {{ $activeSession->meeting_number }}: {{ $activeSession->title }}
                        </h2>
                        <p class="mt-1 text-[12px] text-white/75">
                            {{ $activeSession->session_date?->format('d M Y') }} ·
                            {{ $activeSession->classroom?->name }}
                        </p>
                    </div>
                    <a href="{{ route('mahasiswa.presensi.index') }}"
                        class="inline-flex w-fit items-center gap-2 rounded-full bg-[#C8F24D] px-5 py-3 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0B0B0C] transition hover:bg-[#DAFF73]">
                        Scan QR Sekarang
                        <svg viewBox="0 0 24 24" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                            <path d="M5 12h14m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            </section>
        @endif

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.15fr_1fr]">
            <div class="space-y-4">
                @foreach ($pendingFeedbackSessions as $sess)
                    <section data-aos="fade-up"
                        class="rounded-[26px] border border-indigo-200 bg-[#F3F1FF] p-5 sm:p-6">
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-indigo-500">Evaluasi
                            praktikum</p>
                        <h3 class="mt-1 text-lg font-bold leading-snug tracking-[-0.02em]">
                            Feedback Pertemuan {{ $sess->meeting_number }}: {{ $sess->title }}
                        </h3>
                        <p class="mt-1 text-[12px] text-slate-500">
                            Praktikum telah selesai. Berikan tanggapan agar kualitas praktikum semakin baik.
                        </p>

                        <form action="{{ route('mahasiswa.feedback.store', $sess) }}" method="POST"
                            class="mt-4 space-y-3.5">
                            @csrf
                            <div>
                                <label for="rating-{{ $sess->id }}"
                                    class="block text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500">
                                    Rating pemahaman & penyampaian asisten
                                </label>
                                <select id="rating-{{ $sess->id }}" name="rating" required
                                    class="mt-1.5 w-full rounded-xl border border-indigo-200 bg-white px-3.5 py-2.5 text-[13px] text-[#0B0B0C] focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20 md:w-72">
                                    <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Baik)</option>
                                    <option value="4">⭐⭐⭐⭐ (4 - Baik)</option>
                                    <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                    <option value="2">⭐⭐ (2 - Kurang)</option>
                                    <option value="1">⭐ (1 - Sangat Kurang)</option>
                                </select>
                            </div>
                            <div>
                                <label for="comment-{{ $sess->id }}"
                                    class="block text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500">
                                    Komentar / tanggapan
                                </label>
                                <textarea id="comment-{{ $sess->id }}" name="comment" rows="3" required
                                    placeholder="Tuliskan tanggapan Anda mengenai proses praktikum..."
                                    class="mt-1.5 w-full rounded-xl border border-indigo-200 bg-white px-3.5 py-2.5 text-[13px] text-[#0B0B0C] placeholder:text-slate-500 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-400/20"></textarea>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-full bg-[#0B0B0C] px-5 py-2.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-white transition hover:bg-indigo-700">
                                Kirim Tanggapan
                            </button>
                        </form>
                    </section>
                @endforeach

                <section data-aos="fade-up" class="rounded-[26px] border border-black/5 bg-white p-5 sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Informasi
                            </p>
                            <h2 class="mt-1 text-lg font-bold tracking-[-0.02em]">Pemberitahuan Terbaru</h2>
                        </div>
                        <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#EAF4FF] text-[#0A66C8]">
                            <x-nav-icon name="megaphone" class="h-4 w-4" />
                        </span>
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
                                    <span
                                        class="text-[10px] text-slate-500">{{ $info->created_at->format('d M Y H:i') }}</span>
                                </div>
                                <h3 class="mt-2 text-[13px] font-bold leading-snug">{{ $info->title }}</h3>
                                <p class="mt-1 text-[12px] leading-relaxed text-slate-500">{{ $info->content }}</p>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-black/10 px-4 py-10 text-center">
                                <p class="text-[13px] font-semibold text-slate-500">Belum ada pemberitahuan</p>
                                <p class="mt-1 text-[11px] text-slate-500">Info dari asisten akan tampil di sini.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="space-y-4">
                <section data-aos="fade-up" data-aos-delay="80"
                    class="rounded-[26px] border border-black/5 bg-white p-5 sm:p-6">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-slate-500">Menu Cepat</p>
                    <h2 class="mt-1 text-lg font-bold tracking-[-0.02em]">Langkah Praktikum</h2>
                    <div class="mt-4 space-y-2">
                        @php
                            $steps = [
                                ['label' => 'Scan presensi QR', 'href' => route('mahasiswa.presensi.index'), 'icon' => 'qr'],
                                ['label' => 'Kerjakan pre-test', 'href' => route('mahasiswa.pretest.index'), 'icon' => 'clock'],
                                ['label' => 'Upload tugas PDF', 'href' => route('mahasiswa.tugas.index'), 'icon' => 'doc'],
                                ['label' => 'Lihat transparansi nilai', 'href' => route('mahasiswa.nilai.index'), 'icon' => 'chart'],
                            ];
                        @endphp
                        @foreach ($steps as $step)
                            <a href="{{ $step['href'] }}"
                                class="group flex items-center gap-3 rounded-xl border border-transparent px-3 py-2.5 text-[12.5px] font-semibold text-slate-600 transition hover:border-black/5 hover:bg-[#F5F5F2] hover:text-[#0B0B0C]">
                                <span
                                    class="grid h-8 w-8 place-items-center rounded-lg bg-[#F5F5F2] text-slate-500 transition group-hover:bg-[#0B0B0C] group-hover:text-[#C8F24D]">
                                    <x-nav-icon name="{{ $step['icon'] }}" class="h-4 w-4" />
                                </span>
                                {{ $step['label'] }}
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

                <section data-aos="fade-up" data-aos-delay="140"
                    class="relative isolate overflow-hidden rounded-[26px] border border-black/5 bg-[#F7FFD9] p-5 sm:p-6">
                    <div class="pointer-events-none absolute -right-10 -top-10 h-36 w-36 rounded-full bg-[#C8F24D]/50 blur-2xl"
                        aria-hidden="true"></div>
                    <p class="relative text-[9px] font-extrabold uppercase tracking-[0.18em] text-[#3F5200]">Catatan
                    </p>
                    <p class="relative mt-2 text-[13px] font-semibold leading-relaxed text-[#2B3A00]">
                        Jawaban pre-test ditulis di kertas dan dikumpulkan fisik. Jangan lupa scan QR tepat waktu agar
                        kehadiran tercatat.
                    </p>
                    <a href="{{ route('mahasiswa.presensi.index') }}"
                        class="relative mt-4 inline-flex items-center gap-1.5 text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#0B0B0C] hover:underline">
                        Buka presensi
                        <svg viewBox="0 0 24 24" fill="none" class="h-3 w-3" aria-hidden="true">
                            <path d="M5 12h14m0 0-5-5m5 5-5 5" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
