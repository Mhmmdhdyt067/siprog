@php
    $labels = [
        'cpp' => 'C++',
        'php' => 'PHP',
        'python' => 'Python',
        'java' => 'Java',
        'javascript' => 'JavaScript',
        'html' => 'HTML',
        'sql' => 'SQL',
    ];
@endphp

@forelse ($questions as $q)
    @php
        $fill = [
            'question_text' => $q->question_text,
            'code_snippet' => $q->code_snippet,
            'code_language' => $q->code_language,
            'order' => $q->order,
        ];
    @endphp

    <article class="card px-4 py-4 sm:px-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <span
                    class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#0B0B0C] font-mono text-[12px] font-extrabold text-[#C8F24D]">
                    #{{ $q->order }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="chip chip-sky">{{ $labels[$q->code_language] ?? strtoupper($q->code_language) }}</span>
                        <span class="chip {{ $q->code_snippet ? 'chip-lime' : '' }}">
                            {{ $q->code_snippet ? 'Ada potongan kode' : 'Tanpa kode' }}
                        </span>
                    </div>
                    <p class="mt-2 whitespace-pre-line text-[13px] leading-relaxed text-slate-700">
                        {{ $q->question_text }}</p>

                    @if ($q->code_snippet)
                        <pre
                            class="mt-3 overflow-x-auto rounded-xl border border-black/10 bg-[#282C34] p-3.5 text-[11px] leading-relaxed text-slate-200"><code class="language-{{ $q->code_language }}">{{ $q->code_snippet }}</code></pre>
                    @endif
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-1.5 sm:flex-col sm:items-end">
                <button type="button" class="icon-btn" data-form-modal="pretest-modal"
                    data-action="{{ route('admin.pretest.update', $q) }}" data-method="PUT"
                    data-title="Edit Soal Pre-Test" data-submit="Simpan Perubahan"
                    data-modal-description="Perbarui teks soal, potongan kode, bahasa, atau urutannya."
                    data-fill="{{ json_encode($fill) }}" aria-label="Edit soal nomor {{ $q->order }}">
                    <x-nav-icon name="edit" class="h-3.5 w-3.5" />
                </button>
                <button type="button" class="icon-btn hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                    data-delete="{{ route('admin.pretest.destroy', $q) }}" data-title="Hapus soal pre-test?"
                    data-message="Soal ini akan hilang dari tampilan pre-test mahasiswa." data-confirm="Ya, hapus soal"
                    aria-label="Hapus soal nomor {{ $q->order }}">
                    <x-nav-icon name="trash" class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    </article>
@empty
    <div class="card px-5 py-12 text-center">
        <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#EAF4FF] text-[#0A66C8]">
            <x-nav-icon name="doc" class="h-5 w-5" />
        </span>
        <p class="mt-3 text-[14px] font-bold">Belum ada soal pre-test</p>
        <p class="mt-1 text-[12px] text-slate-500">Tambahkan soal agar mahasiswa dapat mengerjakan pre-test sesi ini.
        </p>
    </div>
@endforelse
