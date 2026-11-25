<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

class AlproDataSeeder extends Seeder
{
    private const COURSE_NAME = 'Algoritma Pemrograman';

    private const PLACEHOLDER = 'Belum diatur';

    private const WORKBOOK = 'PRAKTIKUM MATAKULIAH ALGORITMA PEMROGRAMAN (ALPRO).xlsx';

    /**
     * Replace the old demo data with the real ALPRO classes, assistants, and students.
     */
    public function run(): void
    {
        $classes = $this->readWorkbook();

        $adminPassword = env('SEED_ADMIN_PASSWORD') ?: Str::password(16);

        $this->wipeExistingData();

        DB::transaction(function () use ($classes, $adminPassword) {
            $this->createUser([
                'name' => 'Admin Lab SI-PROG',
                'nim_nip' => 'ADM001',
                'email' => env('SEED_ADMIN_EMAIL', 'admin@siprog.indonesiaemas45.id'),
                'password' => $adminPassword,
                'role' => UserRole::ADMIN,
            ]);

            foreach ($classes as $classData) {
                $classroom = Classroom::create([
                    'code' => $classData['code'],
                    'name' => $classData['name'],
                    'course_name' => self::COURSE_NAME,
                    'schedule_day' => self::PLACEHOLDER,
                    'schedule_time' => self::PLACEHOLDER,
                    'room' => self::PLACEHOLDER,
                ]);

                $assistant = $this->createUser([
                    'name' => $classData['assistant']['name'],
                    'nim_nip' => $classData['assistant']['nim'],
                    'email' => Str::lower($classData['assistant']['nim']).'@asisten.siprog.test',
                    'password' => $classData['assistant']['nim'],
                    'role' => UserRole::ASISTEN,
                ]);

                $assistant->assistedClassrooms()->sync([$classroom->id]);

                foreach ($classData['students'] as $student) {
                    $this->createUser([
                        'name' => $student['name'],
                        'nim_nip' => $student['nim'],
                        'email' => Str::lower($student['nim']).'@mahasiswa.siprog.test',
                        'password' => $student['nim'],
                        'role' => UserRole::MAHASISWA,
                        'classroom_id' => $classroom->id,
                    ]);
                }

                $this->command?->line(sprintf(
                    '  %s: %d mahasiswa, asisten %s',
                    $classroom->name,
                    count($classData['students']),
                    $assistant->name,
                ));
            }
        });

        $studentCount = collect($classes)->sum(fn (array $class) => count($class['students']));

        $this->command?->info(sprintf(
            'Data ALPRO berhasil dibuat: %d kelas, %d mahasiswa, %d asisten, 1 admin.',
            count($classes),
            $studentCount,
            count($classes),
        ));
        if (! env('SEED_ADMIN_PASSWORD')) {
            $this->command?->warn("Kata sandi admin sementara: {$adminPassword} — simpan, lalu ganti setelah login pertama.");
        }

        $this->command?->warn('Akun mahasiswa & asisten memakai NIM sebagai kata sandi awal. Wajib diganti sebelum dipakai di server publik.');
    }

    private function createUser(array $attributes): User
    {
        $user = User::create([
            'name' => $attributes['name'],
            'nim_nip' => $attributes['nim_nip'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
            'role' => $attributes['role'],
            'classroom_id' => $attributes['classroom_id'] ?? null,
            'phone' => $attributes['phone'] ?? null,
        ]);

        // Imported accounts do not use email verification; email remains a contact field.
        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }

    /**
     * @return array<int, array{code: string, name: string, assistant: array{name: string, nim: string}, students: array<int, array{name: string, nim: string}>}>
     */
    private function readWorkbook(): array
    {
        $path = collect([
            base_path(self::WORKBOOK),
            database_path('data/alpro.xlsx'),
        ])->first(fn (string $candidate) => is_file($candidate));

        if (! $path) {
            throw new RuntimeException(
                'File Excel ALPRO tidak ditemukan. Letakkan '.self::WORKBOOK.' di root proyek atau database/data/alpro.xlsx.'
            );
        }

        $book = IOFactory::createReader('Xlsx')->setReadDataOnly(true)->load($path);
        $classes = [];
        $seenNames = [];
        $seenNims = [];

        foreach ($book->getWorksheetIterator() as $sheet) {
            $title = Str::squish($sheet->getTitle());
            $assistantRaw = trim((string) $sheet->getCell('F2')->getValue());

            if (! preg_match('/^(.+?)\s*\(([^()]+)\)\s*$/u', $assistantRaw, $matches)) {
                throw new RuntimeException("Format asisten pada sheet {$title} tidak dikenali: {$assistantRaw}");
            }

            $assistantName = Str::squish($matches[1]);
            $assistantNim = trim($matches[2]);
            $this->assertUniqueIdentity($assistantName, $assistantNim, $seenNames, $seenNims);

            $students = [];

            for ($row = 2; $row <= $sheet->getHighestDataRow(); $row++) {
                $nim = trim((string) $sheet->getCell('B'.$row)->getValue());
                $name = Str::squish((string) $sheet->getCell('C'.$row)->getValue());

                if ($nim === '' && $name === '') {
                    continue;
                }

                if ($nim === '' || $name === '') {
                    throw new RuntimeException("Data mahasiswa tidak lengkap pada sheet {$title}, baris {$row}.");
                }

                $this->assertUniqueIdentity($name, $nim, $seenNames, $seenNims);
                $students[] = ['name' => $name, 'nim' => $nim];
            }

            if ($students === []) {
                throw new RuntimeException("Sheet {$title} tidak memiliki data mahasiswa.");
            }

            $classes[] = [
                'code' => Str::upper(Str::slug($title, '-')),
                'name' => $title,
                'assistant' => ['name' => $assistantName, 'nim' => $assistantNim],
                'students' => $students,
            ];
        }

        if ($classes === []) {
            throw new RuntimeException('Workbook ALPRO tidak memiliki sheet kelas.');
        }

        return $classes;
    }

    private function assertUniqueIdentity(string $name, string $nim, array &$seenNames, array &$seenNims): void
    {
        $normalizedName = mb_strtolower($name);

        if (isset($seenNames[$normalizedName])) {
            throw new RuntimeException("Nama ganda ditemukan di Excel: {$name}");
        }

        if (isset($seenNims[$nim])) {
            throw new RuntimeException("NIM ganda ditemukan di Excel: {$nim}");
        }

        $seenNames[$normalizedName] = true;
        $seenNims[$nim] = true;
    }

    private function wipeExistingData(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            foreach ([
                'assignment_submissions',
                'assignments',
                'feedbacks',
                'grades',
                'attendances',
                'pretest_questions',
                'announcements',
                'practicum_sessions',
                'classroom_assistant',
                'users',
                'classrooms',
                'password_reset_tokens',
                'sessions',
            ] as $table) {
                DB::table($table)->truncate();
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }
}
