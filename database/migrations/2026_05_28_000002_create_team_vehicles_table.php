<?php
// database/migrations/2026_05_28_000002_create_team_vehicles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('team_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            $table->string('plate');
            $table->string('color')->nullable();
            $table->integer('capacity');
            $table->string('type');
            $table->string('country_origin');
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_vehicles');
    }
};
