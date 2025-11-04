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
        // Schema::table('marks_reports', function (Blueprint $table) {
        //     $table->boolean('official')->default(true)->after('marks');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('marks_reports', function (Blueprint $table) {
        //     $table->dropColumn('official');
        // });
    }
};
