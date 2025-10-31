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
        // Update `mark` column in `exam_questions`
        Schema::table('exam_questions', function (Blueprint $table) {
            // Original: $table->unsignedInteger('mark')->nullable()->default(1)
            $table->float('mark', 2)
                ->nullable()
                ->default(1.0)
                ->change();
        });

        // Update `mark` column in `student_exam_attempts`
        Schema::table('student_exam_attempts', function (Blueprint $table) {
            // Original: $table->integer('mark')->nullable()
            $table->float('mark', 2)
                ->nullable()
                ->change();
        });

        // Update `marks` column in `marks_reports`
        Schema::table('marks_reports', function (Blueprint $table) {
            // Original: $table->unsignedInteger('marks')
            $table->float('marks', 2)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // exam_questions mark back to unsignedInteger
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->unsignedInteger('mark')
                ->nullable()
                ->default(1)
                ->change();
        });

        // student_exam_attempts mark back to integer
        Schema::table('student_exam_attempts', function (Blueprint $table) {
            // We use plain integer as it was the original type (2025_10_07_080423)
            $table->integer('mark')->nullable()->change();
        });

        // marks_reports marks back to unsignedInteger
        Schema::table('marks_reports', function (Blueprint $table) {
            $table->unsignedInteger('marks')->change();
        });
    }
};
