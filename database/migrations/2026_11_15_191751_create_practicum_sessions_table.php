<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practicum_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('meeting_number'); // Pertemuan ke-1, 2, dst
            $table->string('title'); // Topik pertemuan
            $table->text('description')->nullable();
            $table->date('session_date');
            $table->string('qr_token')->unique();
            $table->timestamp('qr_expires_at')->nullable();
            $table->enum('status', ['draft', 'active', 'finished'])->default('draft');
            $table->timestamps();

            $table->unique(['classroom_id', 'meeting_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practicum_sessions');
    }
};
