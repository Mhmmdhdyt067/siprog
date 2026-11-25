<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Classroom;

class PortalController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::orderBy('name')->get();
        $announcements = Announcement::with('classroom')
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();

        return view('welcome', compact('classrooms', 'announcements'));
    }
}
