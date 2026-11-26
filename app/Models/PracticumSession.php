<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PracticumSession extends Model
{
    use HasUuid;

    protected $fillable = [
        'classroom_id',
        'created_by',
        'meeting_number',
        'title',
        'description',
        'session_date',
        'qr_token',
        'qr_expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'qr_expires_at' => 'datetime',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function qrTokens(): HasMany
    {
        return $this->hasMany(QrToken::class);
    }

    public function pretestQuestions(): HasMany
    {
        return $this->hasMany(PretestQuestion::class)->orderBy('order');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
