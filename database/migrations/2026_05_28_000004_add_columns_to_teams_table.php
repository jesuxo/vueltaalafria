<?php
// database/migrations/2026_05_28_000004_add_columns_to_teams_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('teams', 'password_hash')) {
                $table->string('password_hash')->nullable();
            }
            if (!Schema::hasColumn('teams', 'access_code')) {
                $table->string('access_code')->unique()->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'password_hash', 'access_code']);
        });
    }
};
