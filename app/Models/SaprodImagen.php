<?php
// app/Models/SaprodImagen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaprodImagen extends Model
{
    use HasFactory;

    protected $table = 'saprod_imagenes';
    protected $fillable = ['producto_id', 'ruta_imagen', 'es_principal', 'orden', 'nombre_original', 'comercial_id'];

    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'producto_id');
    }
}
