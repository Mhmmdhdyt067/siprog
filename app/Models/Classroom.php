<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasUuid;

    protected $fillable = [
        'code',
        'name',
        'course_name',
        'schedule_day',
        'schedule_time',
        'room',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', UserRole::MAHASISWA);
    }

    public function assistants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_assistant', 'classroom_id', 'user_id')
            ->withTimestamps();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(PracticumSession::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
