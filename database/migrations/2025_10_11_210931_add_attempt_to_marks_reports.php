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
        Schema::table('marks_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('attempt_id')->nullable()->after('exam_id');
            $table->foreign('attempt_id')->references('id')->on('student_exam_attempts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marks_reports', function (Blueprint $table) {
            $table->dropForeign('attempt_id');
            $table->dropIndex('attempt_id');
            $table->dropColumn('attempt_id');
        });
    }
};
