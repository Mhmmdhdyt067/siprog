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

class StudentOverallGradesExport implements FromCollection, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping
{
    use BindsStringsExplicitly;

    protected $sessions;

    public function __construct(protected ?int $classroomId = null)
    {
        $this->sessions = PracticumSession::when($classroomId, fn ($q) => $q->where('classroom_id', $classroomId))
            ->orderBy('meeting_number')
            ->get();
    }

    public function collection(): Enumerable
    {
        return User::where('role', UserRole::MAHASISWA)
            ->when($this->classroomId, fn ($q) => $q->where('classroom_id', $this->classroomId))
            ->with(['classroom', 'grades.session', 'attendances'])
            ->orderBy('classroom_id')
            ->orderBy('nim_nip')
            ->get();
    }

    public function headings(): array
    {
        $base = ['NIM', 'Nama Mahasiswa', 'Kelas'];
        $meetingCols = $this->sessions->pluck('meeting_number')
            ->unique()
            ->map(fn ($num) => "Pertemuan {$num}")
            ->toArray();

        return array_merge($base, $meetingCols, [
            'Persentase Kehadiran (%)',
            'Rata-Rata Nilai',
            'Status Kelulusan',
        ]);
    }

    public function map($student): array
    {
        $row = [
            $student->nim_nip,
            $student->name,
            $student->classroom?->name ?? '-',
        ];

        $meetingNumbers = $this->sessions->pluck('meeting_number')->unique();
        foreach ($meetingNumbers as $num) {
            $grade = $student->grades->first(fn ($g) => $g->session?->meeting_number == $num);
            $row[] = $grade ? $grade->total_score : 0;
        }

        $status = $student->graduation_status;
        $row[] = $status['attendance_percentage'].'%';
        $row[] = $status['average_score'];
        $row[] = $status['label'];

        return $row;
    }
}
