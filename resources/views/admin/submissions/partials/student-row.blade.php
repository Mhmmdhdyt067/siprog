@php
    $sub = $mhs->submissions->first();
    $grade = $mhs->grades->first();
    $hasScore = $grade?->assignment_score !== null;
    $filename = $sub?->original_filename ?: ($sub ? basename($sub->file_path) : null);
@endphp

<div class="flex flex-col gap-4 px-4 py-4 sm:px-5 lg:flex-row lg:items-start lg:justify-between">
    <div class="flex min-w-0 items-start gap-3.5">
        <span
            class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#0B0B0C] text-[11px] font-extrabold uppercase text-white">
            {{ mb_substr($mhs->name, 0, 1) }}
        </span>

        <div class="min-w-0 space-y-1">
            <div class="flex flex-wrap items-center gap-1.5">
                <p class="text-[13px] font-bold leading-tight">{{ $mhs->name }}</p>
                <span class="chip {{ $sub ? 'chip-emerald' : 'chip-rose' }}">
                    {{ $sub ? 'Sudah kumpul' : 'Belum kumpul' }}
                </span>
                @if ($hasScore)
                    <span class="chip chip-lime">Nilai {{ number_format((float) $grade->assignment_score, 2) }}</span>
                @endif
            </div>

            <p class="font-mono text-[10px] text-slate-500">{{ $mhs->nim_nip }}</p>

            @if ($sub)
                <p class="inline-flex items-center gap-1 text-[10.5px] text-slate-500">
                    <x-nav-icon name="clock" class="h-3 w-3" />
                    {{ $sub->submitted_at?->locale('id')->translatedFormat('d M Y · H:i') ?? 'Waktu tidak tercatat' }}
                </p>
                <p class="max-w-[16rem] truncate font-mono text-[10px] text-slate-400 sm:max-w-xs" title="{{ $filename }}">
                    {{ $filename }}
                </p>
            @else
                <p class="text-[10.5px] text-slate-500">Belum ada PDF yang diunggah.</p>
            @endif
        </div>
    </div>

    <div class="flex flex-col gap-2.5 sm:flex-row sm:items-start lg:justify-end">
        <div class="flex flex-wrap items-center gap-1.5">
            @if ($sub)
                <button type="button" class="btn btn-light btn-sm" data-preview
                    data-url="{{ route('submissions.view', $sub) }}"
                    data-download="{{ route('submissions.view', ['submission' => $sub, 'download' => 1]) }}"
                    data-name="{{ $mhs->name }} — {{ $filename }}">
                    <x-nav-icon name="eye" class="h-3.5 w-3.5" />
                    Preview
                </button>
                <a href="{{ route('submissions.view', ['submission' => $sub, 'download' => 1]) }}"
                    class="btn btn-light btn-sm">
                    <x-nav-icon name="download" class="h-3.5 w-3.5" />
                    Download
                </a>
            @else
                <span class="chip">Tanpa berkas</span>
            @endif
        </div>

        <form data-ajax method="POST" action="{{ route('admin.submissions.grade', [$assignment, $mhs]) }}"
            class="w-full sm:w-auto">
            @csrf
            <div data-field-group>
                <div class="flex items-start gap-2">
                    <label for="score-{{ $mhs->id }}" class="sr-only">Nilai tugas {{ $mhs->name }}</label>
                    <input id="score-{{ $mhs->id }}" name="assignment_score" type="number" inputmode="decimal"
                        min="0" max="100" step="0.01"
                        value="{{ $hasScore ? (float) $grade->assignment_score : '' }}" placeholder="0–100"
                        class="field-input mt-0 w-[5.5rem] text-center font-mono text-[12px]"
                        @disabled(!$sub)>
                    <button type="submit" class="btn btn-primary btn-sm" @disabled(!$sub)>
                        <x-nav-icon name="check" class="h-3.5 w-3.5" />
                        Simpan
                    </button>
                </div>
                <p data-error="assignment_score" class="mt-1 hidden text-[10px] font-semibold text-rose-600"></p>
                @unless ($sub)
                    <p class="mt-1 text-[9.5px] text-slate-400">Nilai aktif setelah PDF masuk.</p>
                @endunless
            </div>
        </form>
    </div>
</div>
