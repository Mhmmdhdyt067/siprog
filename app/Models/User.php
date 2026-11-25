<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuid, Notifiable;

    protected $fillable = [
        'name',
        'nim_nip',
        'email',
        'password',
        'role',
        'classroom_id',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    // Kelas milik mahasiswa
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    // Kelas yang diampu oleh asisten
    public function assistedClassrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_assistant', 'user_id', 'classroom_id')
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    // Helper status kelulusan / memenuhi syarat di Profile Mahasiswa & Export Admin
    public function getGraduationStatusAttribute(): array
    {
        $totalFinishedSessions = $this->classroom
            ? $this->classroom->sessions()->where('status', 'finished')->count()
            : 0;

        $attendedCount = $this->attendances()
            ->where('status', 'hadir')
            ->whereHas('session', fn ($query) => $query
                ->where('classroom_id', $this->classroom_id)
                ->where('status', 'finished'))
            ->count();
        $avgScore = $this->grades()
            ->whereHas('session', fn ($query) => $query
                ->where('classroom_id', $this->classroom_id)
                ->where('status', 'finished'))
            ->avg('total_score') ?? 0;

        $attendancePct = $totalFinishedSessions > 0
            ? round(($attendedCount / $totalFinishedSessions) * 100, 1)
            : 0;

        // Kriteria: Kehadiran >= 75% DAN rata-rata nilai >= 70
        $isPassed = $totalFinishedSessions > 0 && $attendancePct >= 75 && $avgScore >= 70;

        return [
            'total_sessions' => $totalFinishedSessions,
            'attended_count' => $attendedCount,
            'attendance_percentage' => $attendancePct,
            'average_score' => round($avgScore, 2),
            'is_passed' => $isPassed,
            'label' => $isPassed ? 'MEMENUHI / LULUS' : 'BELUM MEMENUHI',
        ];
    }
}
