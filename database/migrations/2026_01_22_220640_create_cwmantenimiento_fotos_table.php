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
        Schema::create('cwmantenimiento_fotos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mantenimiento_id');
            $table->string('ruta_foto', 255);
            $table->string('nombre_original', 255);
            $table->enum('tipo_evidencia', ['cambio_aceite', 'producto_usado', 'kilometraje', 'general'])->default('general');
            $table->text('descripcion')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
            $table->index('mantenimiento_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cwmantenimiento_fotos');
    }
};
