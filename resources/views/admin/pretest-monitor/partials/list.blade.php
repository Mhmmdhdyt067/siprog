@forelse ($attempts as $attempt)
    @php
        $remaining = $attempt->pretestRemainingSeconds();
        $violations = (int) $attempt->pretest_violations;
        $locked = $attempt->pretest_locked_at !== null;
    @endphp

    <article class="card px-4 py-4 sm:px-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="flex min-w-0 items-start gap-3.5">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
                    <x-nav-icon name="user" class="h-5 w-5" />
                </span>

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="chip chip-sky">{{ $attempt->session?->classroom?->name ?? 'Kelas' }}</span>
                        <span class="chip">Pertemuan {{ $attempt->session?->meeting_number ?? '—' }}</span>
                        @if ($locked)
                            <span class="chip chip-rose">Terkunci</span>
                        @elseif ($violations > 0)
                            <span class="chip chip-amber">Indikasi curang</span>
                        @else
                            <span class="chip chip-lime">Normal</span>
                        @endif
                        @if ($attempt->pretest_warning_at)
                            <span class="chip">Ditegur {{ $attempt->pretest_warning_at->format('H:i') }}</span>
                        @endif
                    </div>

                    <h3 class="mt-2 text-[15px] font-bold leading-snug tracking-[-0.01em]">
                        {{ $attempt->user?->name ?? 'Praktikan' }}
                    </h3>
                    <p class="mt-0.5 text-[11px] font-semibold text-slate-500">
                        {{ $attempt->user?->nim_nip ?? '—' }} · {{ $attempt->session?->title ?? '—' }}
                    </p>

                    <div
                        class="mt-2.5 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[10px] font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-1">
                            <x-nav-icon name="clock" class="h-3 w-3" />
                            Sisa {{ gmdate('i:s', $remaining) }}
                        </span>
                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span class="inline-flex items-center gap-1">
                            <x-nav-icon name="eye" class="h-3 w-3" />
                            {{ $violations }} pelanggaran
                        </span>
                        @if ($attempt->pretest_last_violation_at)
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span>Terakhir {{ $attempt->pretest_last_violation_at->format('H:i:s') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-2">
                <button type="button" class="btn btn-light btn-sm" data-form-modal="warn-modal"
                    data-action="{{ route('admin.pretest.monitor.warn', $attempt) }}" data-method="POST"
                    data-title="Tegur {{ $attempt->user?->name ?? 'Praktikan' }}" data-submit="Kirim Teguran"
                    data-modal-description="Teguran tampil sebagai modal di layar mahasiswa saat polling berikutnya."
                    data-fill="{{ json_encode(['message' => 'Asisten mendeteksi indikasi mencontek. Fokus pada pre-test Anda.']) }}"
                    aria-label="Tegur {{ $attempt->user?->name ?? 'praktikan' }}">
                    <x-nav-icon name="megaphone" class="h-3.5 w-3.5" />
                    Tegur
                </button>

                @if ($locked)
                    <button type="button" class="btn btn-primary btn-sm" data-unlock="{{ route('admin.pretest.monitor.unlock', $attempt) }}"
                        aria-label="Buka kunci pre-test {{ $attempt->user?->name ?? 'praktikan' }}">
                        <x-nav-icon name="check" class="h-3.5 w-3.5" />
                        Buka Kunci
                    </button>
                @endif
            </div>
        </div>
    </article>
@empty
    <div class="card px-5 py-10 text-center text-[12.5px] text-slate-500">
        Belum ada praktikan yang sedang mengerjakan pre-test.
    </div>
@endforelse
