<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->timestamp('pretest_deadline')->nullable()->after('pretest_started_at');
            $table->unsignedTinyInteger('pretest_violations')->default(0)->after('pretest_deadline');
            $table->timestamp('pretest_last_violation_at')->nullable()->after('pretest_violations');
            $table->timestamp('pretest_locked_at')->nullable()->after('pretest_last_violation_at');
            $table->string('pretest_warning')->nullable()->after('pretest_locked_at');
            $table->timestamp('pretest_warning_at')->nullable()->after('pretest_warning');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'pretest_deadline',
                'pretest_violations',
                'pretest_last_violation_at',
                'pretest_locked_at',
                'pretest_warning',
                'pretest_warning_at',
            ]);
        });
    }
};
