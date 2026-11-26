<x-app-layout>
    @php
        $avgScore = (float) $graduation['average_score'];
        $attendancePct = (float) $graduation['attendance_percentage'];
        $totalSessions = (int) $graduation['total_sessions'];
        $isPassed = (bool) $graduation['is_passed'];
        $fmt = fn ($value) => $value !== null ? number_format((float) $value, 2) : '—';
        $attendanceProgress = min(100, max(0, $attendancePct));
        $scoreProgress = min(100, max(0, round(($avgScore / 70) * 100)));
    @endphp

    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Transparansi Nilai" title="Transparansi Nilai Praktikum"
            description="Pantau nilai pre-test, tugas, dan nilai tambahan setiap pertemuan. Nilai akhir dihitung otomatis dari 40% pre-test dan 60% tugas.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ auth()->user()->classroom?->name ?? 'Kelas Praktikum' }}</span>
                <span class="chip font-mono">{{ auth()->user()->nim_nip }}</span>
                <span class="chip">{{ $sessions->count() }} pertemuan</span>
                <span class="chip {{ $isPassed ? 'chip-emerald' : 'chip-rose' }}">{{ $graduation['label'] }}</span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('mahasiswa.tugas.index') }}" class="btn btn-light">
                    <x-nav-icon name="doc" class="h-3.5 w-3.5" />
                    Tugas Mingguan
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <x-nav-icon name="dashboard" class="h-3.5 w-3.5" />
                    Dashboard
                </a>
            </x-slot:actions>
        </x-page-header>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <x-stat-card label="Rata-Rata Nilai" :value="number_format($avgScore, 2)" note="Dari seluruh pertemuan dinilai"
                icon="chart" tone="sky" />
            <x-stat-card label="Kehadiran" :value="$attendancePct . '%'" :note="$totalSessions > 0
                ? $graduation['attended_count'] . ' dari ' . $totalSessions . ' sesi selesai'
                : 'Belum ada sesi selesai'"
                icon="check" tone="lime" />
            <x-stat-card label="Status Kelulusan" :value="$isPassed ? 'Lulus' : 'Belum'" :note="$isPassed ? 'Memenuhi seluruh syarat' : 'Periksa syarat di bawah'"
                icon="badge" :tone="$isPassed ? 'emerald' : 'rose'" />
            <x-stat-card label="Pertemuan Dinilai"
                :value="$sessions->count() > 0 ? $gradedCount . ' / ' . $sessions->count() : '—'"
                :note="$sessions->count() === 0
                    ? 'Belum ada pertemuan'
                    : ($assignmentCount > 0
                        ? $submittedCount . ' tugas terkumpul dari ' . $assignmentCount . ' tugas'
                        : 'Belum ada tugas mingguan')"
                icon="table" tone="indigo" />
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
            <section class="card px-4 py-4 sm:px-5" data-aos="fade-up">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-[#0A66C8]">Status Kelulusan
                        </p>
                        <h2 class="mt-1.5 text-[17px] font-bold tracking-[-0.02em]">{{ $graduation['label'] }}</h2>
                    </div>
                    <span class="chip {{ $isPassed ? 'chip-emerald' : 'chip-rose' }}">
                        {{ $isPassed ? 'Syarat terpenuhi' : 'Belum terpenuhi' }}
                    </span>
                </div>

                <p class="mt-1.5 text-[11.5px] leading-relaxed text-slate-500">
                    Status dihitung dari kehadiran minimal 75% dan rata-rata nilai minimal 70.
                </p>

                <div class="mt-4 space-y-3">
                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-slate-500">Kehadiran
                            </p>
                            <p class="font-mono text-[10.5px] font-bold">
                                {{ number_format($attendancePct, 1) }}% <span class="text-slate-400">/ 75%</span>
                            </p>
                        </div>
                        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-[#F5F5F2]">
                            <div class="h-full rounded-full {{ $attendancePct >= 75 ? 'bg-[#C8F24D]' : 'bg-amber-400' }}"
                                style="width: {{ $attendanceProgress }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-slate-500">Rata-Rata
                                Nilai</p>
                            <p class="font-mono text-[10.5px] font-bold">
                                {{ number_format($avgScore, 2) }} <span class="text-slate-400">/ 70</span>
                            </p>
                        </div>
                        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-[#F5F5F2]">
                            <div class="h-full rounded-full {{ $avgScore >= 70 ? 'bg-[#0A66C8]' : 'bg-amber-400' }}"
                                style="width: {{ $scoreProgress }}%"></div>
                        </div>
                    </div>
                </div>

                <p class="mt-4 text-[10.5px] leading-relaxed text-slate-500">
                    Status diperbarui otomatis setiap kali asisten menyimpan nilai pertemuan.
                </p>
            </section>

            <section class="card px-4 py-4 sm:px-5" data-aos="fade-up">
                <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-[#0A66C8]">Komposisi Nilai</p>
                <h2 class="mt-1.5 text-[17px] font-bold tracking-[-0.02em]">Cara Nilai Dihitung</h2>

                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-black/5 bg-[#F9F9F6] px-3.5 py-2.5">
                        <span class="inline-flex items-center gap-2 text-[11.5px] font-semibold text-slate-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#0A66C8]"></span>
                            Pre-Test
                        </span>
                        <span class="chip chip-sky">40%</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-black/5 bg-[#F9F9F6] px-3.5 py-2.5">
                        <span class="inline-flex items-center gap-2 text-[11.5px] font-semibold text-slate-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#C8F24D]"></span>
                            Tugas PDF
                        </span>
                        <span class="chip chip-lime">60%</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-black/5 bg-[#F9F9F6] px-3.5 py-2.5">
                        <span class="inline-flex items-center gap-2 text-[11.5px] font-semibold text-slate-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Nilai tambahan / keaktifan
                        </span>
                        <span class="chip chip-emerald">Tambahan</span>
                    </div>
                </div>

                <p class="mt-3 text-[10.5px] leading-relaxed text-slate-500">
                    Total nilai dibatasi maksimal 100. Catatan asisten ditampilkan di setiap pertemuan bila tersedia.
                </p>
            </section>
        </div>

        <section class="space-y-2.5">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.18em] text-[#0A66C8]">Rincian Nilai</p>
                    <h2 class="mt-1 text-[17px] font-bold tracking-[-0.02em]">Nilai per Pertemuan</h2>
                </div>
                <span class="chip chip-sky">{{ $gradedCount }} dari {{ $sessions->count() }} pertemuan dinilai</span>
            </div>

            @forelse ($sessions as $sess)
                @php
                    $grade = $sess->grades->first();
                    $attendance = $sess->attendances->first();
                    $assignment = $sess->assignment;
                    $submission = $assignment?->submissions->first();

                    $attendanceTone = match ($attendance?->status) {
                        'hadir' => 'chip-emerald',
                        'izin' => 'chip-sky',
                        'sakit' => 'chip-amber',
                        'alpha' => 'chip-rose',
                        default => 'chip',
                    };

                    $sessionTone = match ($sess->status) {
                        'active' => 'chip-lime',
                        'finished' => 'chip-sky',
                        default => 'chip',
                    };

                    $sessionLabel = match ($sess->status) {
                        'active' => 'Sesi Aktif',
                        'finished' => 'Sesi Selesai',
                        default => 'Draft',
                    };
                @endphp

                <article class="card overflow-hidden transition hover:border-black/10" data-aos="fade-up">
                    <div class="flex flex-col gap-4 px-4 py-4 sm:px-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex min-w-0 items-start gap-3.5">
                            <span
                                class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-[#EAF4FF] font-mono text-[13px] font-extrabold text-[#0A66C8]">
                                {{ str_pad((string) $sess->meeting_number, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="chip {{ $sessionTone }}">{{ $sessionLabel }}</span>
                                    <span class="chip {{ $attendanceTone }}">{{ $attendance ? ucfirst($attendance->status) : 'Tanpa presensi' }}</span>
                                    @if ($assignment)
                                        @if ($submission)
                                            <span class="chip chip-emerald">PDF terkumpul</span>
                                        @elseif ($assignment->deadline?->isPast())
                                            <span class="chip chip-rose">Tugas tidak dikumpulkan</span>
                                        @else
                                            <span class="chip">Tugas belum diunggah</span>
                                        @endif
                                    @endif
                                </div>

                                <h3 class="mt-2 text-[14px] font-bold leading-snug tracking-[-0.01em]">
                                    Pertemuan {{ $sess->meeting_number }} ·
                                    {{ $sess->title }}
                                </h3>

                                <p class="mt-1 text-[10.5px] font-medium text-slate-500">
                                    {{ $sess->session_date?->locale('id')->translatedFormat('l, d M Y') ?? 'Tanggal belum ditentukan' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:w-[22rem]">
                            <div class="rounded-2xl border border-black/5 bg-[#F9F9F6] px-2.5 py-2 text-center">
                                <p class="text-[8px] font-extrabold uppercase tracking-[0.14em] text-slate-500">Pre-Test</p>
                                <p class="mt-1 font-mono text-[12px] font-bold text-[#0A66C8]">
                                    {{ $fmt($grade?->pretest_score) }}</p>
                            </div>
                            <div class="rounded-2xl border border-black/5 bg-[#F9F9F6] px-2.5 py-2 text-center">
                                <p class="text-[8px] font-extrabold uppercase tracking-[0.14em] text-slate-500">Tugas</p>
                                <p class="mt-1 font-mono text-[12px] font-bold text-[#3F5200]">
                                    {{ $fmt($grade?->assignment_score) }}</p>
                            </div>
                            <div class="rounded-2xl border border-black/5 bg-[#F9F9F6] px-2.5 py-2 text-center">
                                <p class="text-[8px] font-extrabold uppercase tracking-[0.14em] text-slate-500">Tambahan
                                </p>
                                <p class="mt-1 font-mono text-[12px] font-bold text-emerald-600">
                                    {{ $grade?->extra_score !== null ? '+' . $fmt($grade->extra_score) : '—' }}</p>
                            </div>
                            <div class="rounded-2xl bg-[#0B0B0C] px-2.5 py-2 text-center">
                                <p class="text-[8px] font-extrabold uppercase tracking-[0.14em] text-white/60">Total</p>
                                <p class="mt-1 font-mono text-[12px] font-extrabold text-[#C8F24D]">
                                    {{ $fmt($grade?->total_score) }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($grade?->notes)
                        <div class="border-t border-black/5 bg-[#FBFBF9] px-4 py-3 sm:px-5">
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-slate-500">Catatan
                                Asisten</p>
                            <p class="mt-1 text-[11.5px] leading-relaxed text-slate-600">{{ $grade->notes }}</p>
                        </div>
                    @endif
                </article>
            @empty
                <div class="card px-5 py-12 text-center" data-aos="fade-up">
                    <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
                        <x-nav-icon name="chart" class="h-5 w-5" />
                    </span>
                    <p class="mt-3 text-[14px] font-bold">Belum ada pertemuan</p>
                    <p class="mt-1 text-[12px] text-slate-500">
                        Nilai akan tampil setelah asisten membuka sesi praktikum dan menyimpan penilaian.
                    </p>
                </div>
            @endforelse
        </section>
    </div>
</x-app-layout>
