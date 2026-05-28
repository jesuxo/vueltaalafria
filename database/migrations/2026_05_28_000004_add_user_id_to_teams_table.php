<?php
// database/migrations/2026_05_28_000004_add_user_id_to_teams_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('password_hash')->nullable(); // Para acceso sin usuario Laravel
            $table->string('access_code')->unique()->nullable(); // Código de acceso para el equipo
        });
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'password_hash', 'access_code']);
        });
    }
};
