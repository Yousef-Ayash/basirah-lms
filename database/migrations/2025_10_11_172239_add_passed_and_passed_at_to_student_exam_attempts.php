<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_exam_attempts', function (Blueprint $table) {
            $table->boolean('passed')->nullable()->after('scored_at');
            $table->timestamp('passed_at')->nullable()->after('passed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_exam_attempts', function (Blueprint $table) {
            $table->dropColumn('passed');
            $table->dropColumn('passed_at');
        });
    }
};
