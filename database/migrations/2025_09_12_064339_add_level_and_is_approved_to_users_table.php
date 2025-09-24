<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // optional level relation (nullable, no FK now to avoid order issues)
            $table->unsignedBigInteger('level_id')->nullable()->after('password')->index();
            // approval flag (default false)
            $table->boolean('is_approved')->default(false)->after('level_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['level_id', 'is_approved']);
        });
    }
};
