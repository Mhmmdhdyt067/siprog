<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Data Master" title="Data Kelas Praktikum"
            description="Kelola identitas kelas, mata kuliah, jadwal, dan ruang laboratorium yang dipakai sepanjang semester.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $stats['classrooms'] }} kelas</span>
                <span class="chip">{{ $stats['students'] }} mahasiswa</span>
                <span class="chip">{{ $stats['assistants'] }} asisten</span>
            </x-slot:meta>
            <x-slot:actions>
                <button type="button" class="btn btn-primary" data-form-modal="classroom-modal"
                    data-action="{{ route('admin.classrooms.store') }}" data-method="POST"
                    data-title="Tambah Kelas" data-submit="Simpan Kelas"
                    data-modal-description="Kode kelas harus unik dan dipakai sebagai identitas singkat kelas.">
                    <x-nav-icon name="plus" class="h-3.5 w-3.5" />
                    Tambah Kelas
                </button>
            </x-slot:actions>
        </x-page-header>

        <div id="classrooms-summary">
            @include('admin.master.partials.classroom-summary', ['stats' => $stats])
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <label class="relative flex-1 sm:max-w-md">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-nav-icon name="search" class="h-3.5 w-3.5" />
                    </span>
                    <input id="classroom-search" type="search" placeholder="Cari kode, kelas, mata kuliah, atau ruang…"
                        class="field-input mt-0 pl-9" autocomplete="off">
                </label>
                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                    <span id="classroom-count">{{ $classrooms->count() }}</span> kelas ditemukan
                </p>
            </div>
        </section>

        <div id="classrooms-list" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
            @include('admin.master.partials.classroom-list', ['classrooms' => $classrooms])
        </div>
    </div>

    <x-modal name="classroom-modal" max-width="2xl" title="Tambah Kelas"
        description="Kode kelas harus unik dan dipakai sebagai identitas singkat kelas.">
        <form data-ajax action="{{ route('admin.classrooms.store') }}" method="POST" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div data-field-group>
                    <label class="field-label" for="classroom-code">Kode Kelas</label>
                    <input id="classroom-code" name="code" type="text" class="field-input" required
                        placeholder="TI-2A-ALG">
                    <p data-error="code" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="classroom-name">Nama Kelas</label>
                    <input id="classroom-name" name="name" type="text" class="field-input" required
                        placeholder="TI-2A">
                    <p data-error="name" class="field-error hidden"></p>
                </div>

                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="classroom-course">Mata Kuliah Praktikum</label>
                    <input id="classroom-course" name="course_name" type="text" class="field-input" required
                        placeholder="Algoritma & Struktur Data">
                    <p data-error="course_name" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="classroom-day">Hari</label>
                    <input id="classroom-day" name="schedule_day" type="text" class="field-input" required
                        placeholder="Senin">
                    <p data-error="schedule_day" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="classroom-time">Jam</label>
                    <input id="classroom-time" name="schedule_time" type="text" class="field-input" required
                        placeholder="08:00 - 10:30 WIB">
                    <p data-error="schedule_time" class="field-error hidden"></p>
                </div>

                <div data-field-group class="sm:col-span-2">
                    <label class="field-label" for="classroom-room">Ruang Lab</label>
                    <input id="classroom-room" name="room" type="text" class="field-input" required
                        placeholder="Lab Pemrograman 1">
                    <p data-error="room" class="field-error hidden"></p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="check" class="h-3.5 w-3.5" />
                    <span data-modal-submit>Simpan Kelas</span>
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterClassrooms = function() {
                    const query = ($('#classroom-search').val() || '').toString().trim().toLowerCase();
                    let visible = 0;

                    $('#classrooms-list [data-classroom-card]').each(function() {
                        const card = $(this);
                        const show = !query || (card.data('search') || '').includes(query);

                        card.toggle(show);
                        if (show) visible++;
                    });

                    $('#classroom-count').text(visible);
                };

                $('#classroom-search').on('input', filterClassrooms);
                $(document).on('sipro:html-applied', filterClassrooms);
            });
        </script>
    @endpush
</x-app-layout>
