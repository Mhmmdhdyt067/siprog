<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practicum_session_id')->constrained('practicum_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('pretest_score', 5, 2)->default(0);
            $table->decimal('assignment_score', 5, 2)->default(0);
            $table->decimal('extra_score', 5, 2)->default(0); // Nilai tambahan / keaktifan
            $table->decimal('total_score', 5, 2)->default(0);
            $table->text('notes')->nullable(); // Catatan evaluasi asisten
            $table->timestamps();

            $table->unique(['practicum_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
