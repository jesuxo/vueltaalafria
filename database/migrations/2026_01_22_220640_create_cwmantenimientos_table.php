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
        Schema::create('cwmantenimientos', function (Blueprint $table) {
            $table->id();
            $table->string('codclie', 50);
            $table->unsignedBigInteger('fk_vehiculo');
            $table->date('fecha_mantenimiento');
            $table->integer('kilometraje')->nullable();
            $table->enum('tipo_mantenimiento', ['cambio_aceite', 'cambio_filtro_aceite', 'cambio_filtro_gasolina', 'cambio_filtro_aire', 'mantenimiento_inyectores', 'bateria', 'otros']);
            $table->string('producto_utilizado', 100)->nullable(); // Aceite, filtro, etc
            $table->string('marca_producto', 50)->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->date('proximo_mantenimiento')->nullable();
            $table->integer('proximo_kilometraje')->nullable();
            $table->string('realizado_por', 50)->nullable(); // usuario que realizó el servicio
            $table->timestamps();
            $table->index(['codclie', 'fk_vehiculo', 'fecha_mantenimiento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cwmantenimientos');
    }
};
