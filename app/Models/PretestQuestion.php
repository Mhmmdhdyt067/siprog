<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PretestQuestion extends Model
{
    use HasUuid;

    protected $fillable = [
        'practicum_session_id',
        'question_text',
        'code_snippet',
        'code_language',
        'order',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(PracticumSession::class, 'practicum_session_id');
    }
}
