<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-4 sm:space-y-5">
        <x-page-header eyebrow="Pre-Test Praktikum"
            :title="'Soal Pertemuan ' . $session->meeting_number . ': ' . $session->title"
            description="Susun soal dan potongan kode yang akan dikerjakan mahasiswa sebelum praktikum dimulai.">
            <x-slot:meta>
                <span class="chip chip-sky">{{ $session->classroom?->name }}</span>
                <span class="chip">{{ $session->session_date?->locale('id')->translatedFormat('l, d M Y') }}</span>
                <span class="chip">{{ $stats['total'] }} soal</span>
            </x-slot:meta>
            <x-slot:actions>
                <a href="{{ route('sessions.index') }}" class="btn btn-light">
                    <x-nav-icon name="arrow-left" class="h-3.5 w-3.5" />
                    Daftar Sesi
                </a>
                <button type="button" class="btn btn-primary" data-form-modal="pretest-modal"
                    data-action="{{ route('admin.pretest.store', $session) }}" data-method="POST"
                    data-title="Tambah Soal Pre-Test" data-submit="Tambah Soal"
                    data-modal-description="Soal diurutkan berdasarkan nomor urut yang diisi."
                    data-fill="{{ json_encode(['code_language' => 'cpp', 'order' => $questions->max('order') ? $questions->max('order') + 1 : 1]) }}">
                    <x-nav-icon name="plus" class="h-3.5 w-3.5" />
                    Tambah Soal
                </button>
            </x-slot:actions>
        </x-page-header>

        <div id="pretest-summary">
            @include('admin.pretest.partials.summary', ['stats' => $stats])
        </div>

        <div id="pretest-list" class="space-y-2.5">
            @include('admin.pretest.partials.list', ['questions' => $questions])
        </div>
    </div>

    <x-modal name="pretest-modal" max-width="2xl" title="Tambah Soal Pre-Test"
        description="Soal diurutkan berdasarkan nomor urut yang diisi.">
        <form data-ajax action="{{ route('admin.pretest.store', $session) }}" method="POST" class="space-y-4">
            <div data-field-group>
                <label class="field-label" for="pretest-question">Pertanyaan / Instruksi</label>
                <textarea id="pretest-question" name="question_text" rows="4" class="field-input" required
                    placeholder="Tulis soal pre-test…"></textarea>
                <p data-error="question_text" class="field-error hidden"></p>
            </div>

            <div data-field-group>
                <label class="field-label" for="pretest-code">Potongan Kode (opsional)</label>
                <textarea id="pretest-code" name="code_snippet" rows="6"
                    class="field-input font-mono text-[12px]" placeholder="// blok kode yang ditampilkan pada soal"></textarea>
                <p class="field-help">Kode akan ditampilkan dengan syntax highlighting sesuai bahasa yang dipilih.</p>
                <p data-error="code_snippet" class="field-error hidden"></p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div data-field-group>
                    <label class="field-label" for="pretest-language">Bahasa Pemrograman</label>
                    <select id="pretest-language" name="code_language" class="field-input" required>
                        <option value="cpp">C++</option>
                        <option value="php">PHP</option>
                        <option value="python">Python</option>
                        <option value="java">Java</option>
                        <option value="javascript">JavaScript</option>
                        <option value="html">HTML</option>
                        <option value="sql">SQL</option>
                    </select>
                    <p data-error="code_language" class="field-error hidden"></p>
                </div>

                <div data-field-group>
                    <label class="field-label" for="pretest-order">Urutan Soal</label>
                    <input id="pretest-order" name="order" type="number" min="1" class="field-input" required>
                    <p data-error="order" class="field-error hidden"></p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-black/5 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" class="btn btn-light" data-modal-close>Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-nav-icon name="check" class="h-3.5 w-3.5" />
                    <span data-modal-submit>Tambah Soal</span>
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
