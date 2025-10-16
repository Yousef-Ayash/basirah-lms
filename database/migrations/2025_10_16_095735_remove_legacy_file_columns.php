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
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('cover_image_path');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });


        // We keep youtube_id but remove file_path
        Schema::table('subject_materials', function (Blueprint $table) {
            $table->dropColumn('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('cover_image_path')->nullable();
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->string('photo_path')->nullable();
        });

        Schema::table('subject_materials', function (Blueprint $table) {
            $table->string('file_path')->nullable();
        });
    }
};
