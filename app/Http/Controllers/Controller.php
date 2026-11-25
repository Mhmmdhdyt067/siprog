<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Classroom;
use App\Models\PracticumSession;
use Illuminate\Support\Collection;

abstract class Controller
{
    protected function managedClassroomIds(): Collection
    {
        $user = auth()->user();

        if ($user->role === UserRole::ADMIN) {
            return Classroom::query()->pluck('id');
        }

        return $user->assistedClassrooms()->pluck('classrooms.id');
    }

    protected function authorizeManagedSession(PracticumSession $session): void
    {
        abort_unless($this->managedClassroomIds()->contains($session->classroom_id), 403);
    }
}
