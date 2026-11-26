<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'practicum_session_id',
        'user_id',
        'rating',
        'comment',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(PracticumSession::class, 'practicum_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
