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
        Schema::create('cwproductosrecomendados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_vehiculo');
            $table->string('tipo_producto', 50); // aceite, filtro_aceite, filtro_gasolina, etc
            $table->string('producto_recomendado', 100);
            $table->string('especificacion', 100)->nullable(); // SAE 20W-50, etc
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cwproductosrecomendados');
    }
};
