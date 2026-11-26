@php
    $grade = $mhs->grades->first();
    $sub = $mhs->submissions->first();
    $score = $grade ? (float) $grade->total_score : null;
    $scoreTone = match (true) {
        $score === null => 'chip',
        $score >= 85 => 'chip-emerald',
        $score >= 70 => 'chip-sky',
        $score >= 50 => 'chip-amber',
        default => 'chip-rose',
    };
    $attendance = $mhs->attendances->first();
    $attendanceTone = match ($attendance?->status) {
        'hadir' => 'chip-emerald',
        'izin' => 'chip-sky',
        'sakit' => 'chip-amber',
        'alpha' => 'chip-rose',
        default => 'chip',
    };
    $fill = [
        'pretest_score' => $grade?->pretest_score ?? 0,
        'assignment_score' => $grade?->assignment_score ?? 0,
        'extra_score' => $grade?->extra_score ?? 0,
        'notes' => $grade?->notes ?? '',
        '__student' => $mhs->name . ' · ' . $mhs->nim_nip,
    ];
    $search = mb_strtolower($mhs->name . ' ' . $mhs->nim_nip);
@endphp

<tr id="grading-row-{{ $mhs->id }}" data-grade-row data-graded="{{ $grade ? '1' : '0' }}"
    data-search="{{ $search }}" class="align-top transition hover:bg-[#F9F9F6]">
    <td class="px-4 py-3.5">
        <div class="flex items-start gap-3">
            <span
                class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#0B0B0C] text-[10px] font-extrabold uppercase text-white">
                {{ mb_substr($mhs->name, 0, 1) }}
            </span>
            <div class="min-w-0">
                <p class="text-[12.5px] font-bold leading-tight">{{ $mhs->name }}</p>
                <p class="mt-0.5 font-mono text-[10px] text-slate-500">{{ $mhs->nim_nip }}</p>
                @if ($grade?->notes)
                    <p class="mt-1 line-clamp-1 max-w-[12rem] text-[10px] text-slate-500"
                        title="{{ $grade->notes }}">Catatan: {{ $grade->notes }}</p>
                @endif
            </div>
        </div>
    </td>

    <td class="px-4 py-3.5">
        <span class="chip {{ $attendanceTone }}">{{ $attendance ? ucfirst($attendance->status) : 'Belum' }}</span>
    </td>

    <td class="hidden px-4 py-3.5 sm:table-cell">
        @if ($sub)
            <a href="{{ route('submissions.view', $sub) }}" target="_blank" rel="noopener"
                class="row-action">
                <x-nav-icon name="eye" class="h-3 w-3" />
                PDF
            </a>
        @else
            <span class="text-[10.5px] font-medium text-slate-400">Belum upload</span>
        @endif
    </td>

    <td class="hidden px-4 py-3.5 font-mono text-[12px] sm:table-cell">{{ $grade?->pretest_score !== null ? number_format((float) $grade->pretest_score, 2) : '—' }}</td>
    <td class="hidden px-4 py-3.5 font-mono text-[12px] sm:table-cell">{{ $grade?->assignment_score !== null ? number_format((float) $grade->assignment_score, 2) : '—' }}</td>
    <td class="hidden px-4 py-3.5 font-mono text-[12px] sm:table-cell">{{ $grade?->extra_score !== null ? number_format((float) $grade->extra_score, 2) : '—' }}</td>

    <td class="px-4 py-3.5">
        <span class="chip {{ $scoreTone }}">{{ $score !== null ? number_format($score, 2) : 'Belum' }}</span>
    </td>

    <td class="px-4 py-3.5 text-right">
        <button type="button" class="btn btn-light btn-sm" data-form-modal="grade-modal"
            data-action="{{ route('grading.student', [$session, $mhs]) }}" data-method="POST"
            data-title="Nilai {{ $mhs->name }}" data-submit="Simpan Nilai"
            data-modal-description="Nilai akhir dihitung otomatis: 40% pre-test, 60% tugas, ditambah nilai tambahan."
            data-fill="{{ json_encode($fill) }}">
            <x-nav-icon name="edit" class="h-3.5 w-3.5" />
            Nilai
        </button>
    </td>
</tr>
