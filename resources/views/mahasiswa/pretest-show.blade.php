<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <!-- Sticky Countdown Header -->
        <div
            class="sticky top-[4.5rem] z-20 mb-6 flex flex-wrap items-center justify-between gap-3 rounded-[22px] border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <div>
                <h2 class="font-bold text-lg">Pre-Test Pertemuan {{ $session->meeting_number }}</h2>
                <p class="text-xs text-slate-500">Tuliskan jawaban di lembar kertas dan kumpulkan ke asisten.</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-500 block">Sisa Waktu</span>
                <span id="countdown-timer" class="text-2xl font-mono font-bold text-amber-600">15:00</span>
            </div>
        </div>

        <!-- Daftar Soal Esai & Code Block (Tanpa Kolom Jawaban) -->
        <div class="space-y-6">
            @foreach ($questions as $q)
                <div class="bg-white border border-black/5 rounded-[22px] p-4 sm:p-6 text-[#0B0B0C]" data-aos="fade-up">
                    <div class="flex items-start gap-3">
                        <span class="px-3 py-1 rounded-lg bg-[#EAF4FF] text-[#0A66C8] font-bold">
                            #{{ $loop->iteration }}
                        </span>
                        <div class="w-full">
                            <p class="text-base leading-relaxed whitespace-pre-line">{{ $q->question_text }}</p>

                            @if ($q->code_snippet)
                                <div class="mt-4 overflow-x-auto rounded-xl border border-[#2C313A]">
                                    <div class="bg-[#21252B] px-4 py-1.5 text-xs uppercase font-mono text-slate-400">
                                        {{ $q->code_language }}
                                    </div>
                                    <pre class="!m-0"><code class="language-{{ $q->code_language }}">{{ $q->code_snippet }}</code></pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form id="finish-pretest-form" action="{{ route('mahasiswa.pretest.finish', $session) }}" method="POST"
            class="mt-8 text-center">
            @csrf
            <button type="submit" id="finish-pretest-button"
                class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg transition">
                Selesai Mengerjakan Pre-Test
            </button>
        </form>
    </div>

    <x-modal name="pretest-violation" max-width="md" title="Anda terdeteksi mencontek">
        <div class="space-y-4 text-[12.5px] leading-relaxed text-slate-600">
            <p>Perpindahan tab atau aplikasi tercatat di server. Waktu pengerjaan Anda dikurangi 5 menit setiap
                pelanggaran.</p>
            <p class="font-semibold text-rose-600">
                Pelanggaran: <span id="violation-count">1</span> dari 2. Pelanggaran kedua akan mengunci akses
                pre-test.
            </p>
            <button type="button" class="btn btn-primary w-full" data-modal-close>Saya mengerti</button>
        </div>
    </x-modal>

    <x-modal name="pretest-warning" max-width="md" title="Pesan dari Asisten">
        <div class="space-y-4 text-[12.5px] leading-relaxed text-slate-600">
            <p id="warning-text"></p>
            <button type="button" class="btn btn-primary w-full" data-modal-close>Mengerti</button>
        </div>
    </x-modal>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let remaining = {{ $remainingSeconds }};
            let away = false;
            let blurTimer = null;
            let lastReport = 0;
            let lastViolations = 0;
            let lastWarningAt = null;
            let pendingNotice = null;

            const COOLDOWN_MS = 5000;
            const BLUR_TOLERANCE_MS = 2000;
            const STATE_POLL_MS = 10000;

            const timerEl = document.getElementById('countdown-timer');
            const finishForm = document.getElementById('finish-pretest-form');
            const finishButton = document.getElementById('finish-pretest-button');
            const violationCount = document.getElementById('violation-count');
            const warningText = document.getElementById('warning-text');

            const violationUrl = "{{ route('mahasiswa.pretest.violation', $session) }}";
            const stateUrl = "{{ route('mahasiswa.pretest.state', $session) }}";
            const dashboardUrl = "{{ route('dashboard') }}";
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            const render = function() {
                const safe = Math.max(0, remaining);
                const mins = String(Math.floor(safe / 60)).padStart(2, '0');
                const secs = String(safe % 60).padStart(2, '0');
                timerEl.textContent = `${mins}:${secs}`;
            };

            const handleState = function(data) {
                if (typeof data.remaining_seconds === 'number') {
                    remaining = data.remaining_seconds;
                    render();
                }

                if (data.finished || data.locked) {
                    window.location.assign(dashboardUrl);
                    return;
                }

                if (typeof data.violations === 'number') {
                    if (data.violations > 0 && data.violations !== lastViolations) {
                        lastViolations = data.violations;
                        violationCount.textContent = data.violations;

                        if (document.hidden) {
                            pendingNotice = true;
                        } else {
                            Sipro.openModal('pretest-violation');
                        }
                    } else if (data.violations === 0) {
                        lastViolations = 0;
                    }
                }
            };

            const checkState = function() {
                fetch(stateUrl, {
                        headers: { Accept: 'application/json' },
                        cache: 'no-store',
                    })
                    .then((response) => response.ok ? response.json() : Promise.reject())
                    .then((data) => {
                        if (data.warning && data.warning.at && data.warning.at !== lastWarningAt) {
                            lastWarningAt = data.warning.at;
                            warningText.textContent = data.warning.message ?? '';
                            Sipro.openModal('pretest-warning');
                        }

                        handleState(data);
                    })
                    .catch(() => {});
            };

            const sendViolation = function(trigger, attempt) {
                fetch(violationUrl, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ trigger: trigger }),
                    })
                    .then((response) => response.ok ? response.json() : Promise.reject())
                    .then((data) => handleState(data))
                    .catch(() => {
                        if (attempt === 0) {
                            setTimeout(() => sendViolation(trigger, 1), 2000);
                        } else {
                            Sipro.toast('Pelanggaran gagal dilaporkan. Jangan berpindah tab lagi.', 'error');
                        }
                    });
            };

            const report = function(trigger) {
                if (away) return;
                away = true;

                if (Date.now() - lastReport < COOLDOWN_MS) return;
                lastReport = Date.now();

                sendViolation(trigger, 0);
            };

            const resume = function() {
                if (!away) return;

                away = false;
                clearTimeout(blurTimer);

                if (pendingNotice) {
                    pendingNotice = null;
                    Sipro.openModal('pretest-violation');
                }

                checkState();
            };

            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    report('tab');
                } else {
                    resume();
                }
            });

            window.addEventListener('blur', function() {
                if (away) return;

                blurTimer = setTimeout(function() {
                    if (!document.hasFocus()) report('window');
                }, BLUR_TOLERANCE_MS);
            });

            window.addEventListener('focus', function() {
                clearTimeout(blurTimer);
                resume();
            });

            finishButton.addEventListener('click', function(event) {
                event.preventDefault();

                Sipro.confirm({
                    title: 'Selesaikan Pre-Test?',
                    message: 'Anda tidak dapat mengakses soal ini lagi setelah selesai.',
                    confirmText: 'Ya, selesaikan',
                    tone: 'danger',
                    onConfirm: function() {
                        return new Promise(function(resolve) {
                            finishForm.submit();
                            resolve();
                        });
                    },
                });
            });

            render();

            const countdown = setInterval(function() {
                if (remaining <= 0) {
                    clearInterval(countdown);
                    render();
                    finishForm.submit();
                    return;
                }

                remaining--;
                render();
            }, 1000);

            setInterval(function() {
                if (document.hidden) return;
                checkState();
            }, STATE_POLL_MS);
        });
    </script>
</x-app-layout>
