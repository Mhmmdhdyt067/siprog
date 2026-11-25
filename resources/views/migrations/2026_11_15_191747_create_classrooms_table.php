<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: TI-2A-PWEB
            $table->string('name'); // Contoh: TI-2A
            $table->string('course_name'); // Contoh: Pemrograman Web
            $table->string('schedule_day'); // Contoh: Senin
            $table->string('schedule_time'); // Contoh: 08:00 - 10:30
            $table->string('room'); // Contoh: Lab Komputasi 1
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
