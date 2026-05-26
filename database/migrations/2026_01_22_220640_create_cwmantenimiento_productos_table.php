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
        Schema::create('cwmantenimientoproductos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mantenimiento_id');
            $table->string('codprod', 50)->nullable();
            $table->string('descripcion', 200);
            $table->string('referencia', 100)->nullable();
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio', 10, 2)->nullable();
            $table->string('tipo', 50)->default('producto'); // producto, servicio, recomendado
            $table->timestamps();
            $table->index('codprod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cwmantenimientoproductos');
    }
};
