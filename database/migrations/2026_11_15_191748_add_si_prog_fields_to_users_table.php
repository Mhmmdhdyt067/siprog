<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim_nip')->unique()->nullable()->after('name');
            $table->string('role')->default(UserRole::MAHASISWA->value)->after('email');
            $table->foreignId('classroom_id')
                ->nullable()
                ->after('role')
                ->constrained('classrooms')
                ->nullOnDelete();
            $table->string('phone')->nullable()->after('classroom_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn(['nim_nip', 'role', 'classroom_id', 'phone']);
        });
    }
};
