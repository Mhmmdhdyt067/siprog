<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'practicum_session_id',
        'user_id',
        'graded_by',
        'pretest_score',
        'assignment_score',
        'extra_score',
        'total_score',
        'notes',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(PracticumSession::class, 'practicum_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
