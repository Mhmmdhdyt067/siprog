<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Pengawasan" title="Monitoring Pre-Test"
            description="Pantau praktikan yang sedang mengerjakan pre-test di kelas Anda. Data diperbarui otomatis setiap 5 detik.">
            <x-slot:meta>
                <span class="chip chip-sky">Diperbarui <span id="monitor-updated">—</span></span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('admin.pretest.overview') }}" class="btn btn-light">
                    <x-nav-icon name="arrow-left" class="h-3.5 w-3.5" />
                    Soal Pre-Test
                </a>
            </x-slot:actions>
        </x-page-header>

        <div id="monitor-summary">
            @include('admin.pretest-monitor.partials.summary', ['stats' => $stats])
        </div>

        <div id="monitor-list" class="space-y-2.5">
            @include('admin.pretest-monitor.partials.list', ['attempts' => $attempts])
        </div>
    </div>

    <x-modal name="warn-modal" max-width="md" title="Tegur Praktikan">
        <form data-ajax action="" method="POST" class="space-y-4">
            @csrf
            <div data-field-group>
                <label class="field-label" for="warn-message">Pesan teguran</label>
                <textarea id="warn-message" name="message" rows="3" maxlength="255" class="field-input" required
                    placeholder="Tulis pesan singkat untuk praktikan…"></textarea>
                <p data-error="message" class="field-error hidden"></p>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="send" class="h-3.5 w-3.5" />
                    <span data-modal-submit>Kirim Teguran</span>
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const updated = document.getElementById('monitor-updated');
                if (!document.getElementById('monitor-list')) return;

                let busy = false;

                const stamp = function() {
                    if (!updated) return;
                    updated.textContent = new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                    });
                };

                const refresh = function() {
                    if (busy || document.hidden) return;
                    busy = true;

                    fetch("{{ route('admin.pretest.monitor.data') }}", {
                            headers: {
                                Accept: 'application/json'
                            },
                            cache: 'no-store',
                        })
                        .then((response) => response.ok ? response.json() : Promise.reject())
                        .then((data) => {
                            Sipro.applyHtml(data.html);
                            stamp();
                        })
                        .catch(() => {})
                        .finally(() => {
                            busy = false;
                        });
                };

                stamp();
                setInterval(refresh, 5000);
                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden) refresh();
                });

                $(document).on('click', '[data-unlock]', function() {
                    const url = this.dataset.unlock;

                    Sipro.confirm({
                        title: 'Buka kunci pre-test?',
                        message: 'Mahasiswa dapat membuka soal lagi. Sisa waktu tidak dipulihkan.',
                        confirmText: 'Buka kunci',
                        tone: 'danger',
                        onConfirm: () => Sipro.post(url)
                            .done((response) => {
                                Sipro.toast(response.message || 'Akses pre-test dibuka.');
                                Sipro.applyHtml(response.html);
                            })
                            .fail((xhr) => Sipro.toast(xhr.responseJSON?.message ||
                                'Gagal membuka kunci.', 'error')),
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
