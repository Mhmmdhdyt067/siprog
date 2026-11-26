<x-app-layout>
    <div class="space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Konten & Tugas" title="Pemberitahuan & Info Tugas"
            description="Publikasikan pengumuman, perubahan jadwal, dan notifikasi tugas ke seluruh kelas atau kelas tertentu.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $stats['total'] }} pemberitahuan</span>
                <span class="chip chip-amber">{{ $stats['tugas'] }} info tugas</span>
            </x-slot:meta>
            <x-slot:actions>
                <button type="button" class="btn btn-primary" data-form-modal="announcement-modal"
                    data-action="{{ route('admin.announcements.store') }}" data-method="POST"
                    data-title="Buat Pemberitahuan" data-submit="Publikasikan"
                    data-modal-description="Pemberitahuan langsung tampil di dashboard mahasiswa terkait."
                    data-fill="{{ json_encode(['type' => 'info']) }}">
                    <x-nav-icon name="plus" class="h-3.5 w-3.5" />
                    Pemberitahuan Baru
                </button>
            </x-slot:actions>
        </x-page-header>

        <div id="announcements-summary">
            @include('admin.announcements.partials.summary', ['stats' => $stats])
        </div>

        <section class="card px-4 py-4 sm:px-5">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" class="btn btn-primary btn-sm" data-announcement-filter="all">Semua</button>
                    <button type="button" class="btn btn-light btn-sm" data-announcement-filter="info">Info</button>
                    <button type="button" class="btn btn-light btn-sm" data-announcement-filter="jadwal">Jadwal</button>
                    <button type="button" class="btn btn-light btn-sm" data-announcement-filter="tugas">Tugas</button>
                </div>

                <div class="flex flex-1 flex-col gap-2 sm:flex-row xl:justify-end">
                    <label class="relative sm:w-72">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <x-nav-icon name="search" class="h-3.5 w-3.5" />
                        </span>
                        <input id="announcement-search" type="search" placeholder="Cari pemberitahuan…"
                            class="field-input mt-0 pl-9" autocomplete="off">
                    </label>
                    <p class="self-center whitespace-nowrap text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500">
                        <span id="announcement-count">{{ $announcements->total() }}</span> tampil
                    </p>
                </div>
            </div>
        </section>

        <div id="announcements-list" class="space-y-2.5">
            @include('admin.announcements.partials.list', ['announcements' => $announcements])
        </div>
    </div>

    <x-modal name="announcement-modal" max-width="2xl" title="Buat Pemberitahuan"
        description="Pemberitahuan langsung tampil di dashboard mahasiswa terkait.">
        <form data-ajax action="{{ route('admin.announcements.store') }}" method="POST" class="space-y-4">
            <div data-field-group>
                <label class="field-label" for="announcement-title">Judul</label>
                <input id="announcement-title" name="title" type="text" class="field-input" required
                    placeholder="Contoh: Perubahan jadwal praktikum pekan ini">
                <p data-error="title" class="field-error hidden"></p>
            </div>

            <div data-field-group>
                <label class="field-label" for="announcement-content">Isi Pemberitahuan</label>
                <textarea id="announcement-content" name="content" rows="5" class="field-input" required
                    placeholder="Tulis informasi lengkap untuk mahasiswa…"></textarea>
                <p data-error="content" class="field-error hidden"></p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div data-field-group>
                    <label class="field-label" for="announcement-type">Tipe</label>
                    <select id="announcement-type" name="type" class="field-input" required>
                        <option value="info">Info umum</option>
                        <option value="jadwal">Jadwal</option>
                        <option value="tugas">Tugas</option>
                    </select>
                    <p data-error="type" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="announcement-classroom">Target Kelas</label>
                    <select id="announcement-classroom" name="classroom_id" class="field-input">
                        <option value="">Semua kelas</option>
                        @foreach ($classrooms as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} — {{ $c->course_name }}</option>
                        @endforeach
                    </select>
                    <p data-error="classroom_id" class="field-error hidden"></p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="send" class="h-3.5 w-3.5" />
                    <span data-modal-submit>Publikasikan</span>
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let activeType = 'all';

                const applyFilters = function() {
                    const query = ($('#announcement-search').val() || '').toString().trim().toLowerCase();
                    let visible = 0;

                    $('#announcements-list [data-announcement-card]').each(function() {
                        const card = $(this);
                        const matchesType = activeType === 'all' || card.data('type') === activeType;
                        const matchesQuery = !query || (card.data('search') || '').includes(query);
                        const show = matchesType && matchesQuery;

                        card.toggle(show);
                        if (show) visible++;
                    });

                    $('#announcement-count').text(visible);
                };

                $('[data-announcement-filter]').on('click', function() {
                    activeType = $(this).data('announcement-filter');

                    $('[data-announcement-filter]').each(function() {
                        const isActive = $(this).data('announcement-filter') === activeType;
                        $(this).toggleClass('btn-primary', isActive)
                            .toggleClass('btn-light', !isActive);
                    });

                    applyFilters();
                });

                $('#announcement-search').on('input', applyFilters);
                $(document).on('sipro:html-applied', applyFilters);
            });
        </script>
    @endpush
</x-app-layout>
