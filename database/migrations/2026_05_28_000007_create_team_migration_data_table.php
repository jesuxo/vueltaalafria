<?php
// database/migrations/2026_05_28_000007_create_team_migration_data_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Datos migratorios del equipo
        Schema::create('team_migration_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->date('arrival_date')->nullable();
            $table->string('arrival_border', 255)->nullable(); // Frontera de arribo
            $table->string('transport_type', 100)->nullable(); // Aéreo, Terrestre
            $table->date('return_date')->nullable();
            $table->time('return_flight_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Vehículos de la delegación
        Schema::create('team_migration_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('brand', 100);
            $table->string('model', 100);
            $table->string('plate', 50);
            $table->integer('year');
            $table->string('color', 50)->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_migration_vehicles');
        Schema::dropIfExists('team_migration_data');
    }
};
