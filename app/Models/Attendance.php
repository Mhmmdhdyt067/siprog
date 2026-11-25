<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasUuid;

    protected $fillable = [
        'practicum_session_id',
        'user_id',
        'status',
        'scanned_at',
        'pretest_started_at',
        'pretest_deadline',
        'pretest_finished_at',
        'pretest_violations',
        'pretest_last_violation_at',
        'pretest_locked_at',
        'pretest_warning',
        'pretest_warning_at',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'pretest_started_at' => 'datetime',
            'pretest_deadline' => 'datetime',
            'pretest_finished_at' => 'datetime',
            'pretest_last_violation_at' => 'datetime',
            'pretest_locked_at' => 'datetime',
            'pretest_warning_at' => 'datetime',
        ];
    }

    public function pretestRemainingSeconds(): int
    {
        if (! $this->pretest_deadline || $this->pretest_finished_at) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($this->pretest_deadline, false));
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(PracticumSession::class, 'practicum_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
