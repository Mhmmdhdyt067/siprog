<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pretest_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practicum_session_id')->constrained('practicum_sessions')->cascadeOnDelete();
            $table->text('question_text'); // Soal esai
            $table->longText('code_snippet')->nullable(); // Text box kode program (opsional)
            $table->string('code_language')->default('php'); // php, python, cpp, javascript, java
            $table->unsignedTinyInteger('order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pretest_questions');
    }
};
