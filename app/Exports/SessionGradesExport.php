<?php

namespace App\Exports;

use App\Enums\UserRole;
use App\Exports\Concerns\BindsStringsExplicitly;
use App\Models\PracticumSession;
use App\Models\User;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SessionGradesExport implements FromCollection, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping
{
    use BindsStringsExplicitly;

    public function __construct(protected PracticumSession $session) {}

    public function collection(): Enumerable
    {
        return User::where('role', UserRole::MAHASISWA)
            ->where('classroom_id', $this->session->classroom_id)
            ->with([
                'attendances' => fn ($q) => $q->where('practicum_session_id', $this->session->id),
                'grades' => fn ($q) => $q->where('practicum_session_id', $this->session->id),
            ])
            ->orderBy('nim_nip')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama Mahasiswa',
            'Kelas',
            'Pertemuan',
            'Status Kehadiran',
            'Nilai Pre-Test',
            'Nilai Tugas',
            'Nilai Tambahan',
            'Total Nilai',
            'Catatan Asisten',
        ];
    }

    public function map($student): array
    {
        $attendance = $student->attendances->first();
        $grade = $student->grades->first();

        return [
            $student->nim_nip,
            $student->name,
            $this->session->classroom->name,
            "Pertemuan {$this->session->meeting_number} - {$this->session->title}",
            strtoupper($attendance?->status ?? 'ALPHA'),
            $grade?->pretest_score ?? 0,
            $grade?->assignment_score ?? 0,
            $grade?->extra_score ?? 0,
            $grade?->total_score ?? 0,
            $grade?->notes ?? '-',
        ];
    }
}
