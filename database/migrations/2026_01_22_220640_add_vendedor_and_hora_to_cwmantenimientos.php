<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cwmantenimientos', function (Blueprint $table) {
            $table->string('codvend', 50)->nullable()->after('realizado_por');
            $table->time('hora_mantenimiento')->nullable()->after('fecha_mantenimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cwmantenimientos', function (Blueprint $table) {
            $table->dropColumn(['codvend', 'hora_mantenimiento']);
        });
    }
};
