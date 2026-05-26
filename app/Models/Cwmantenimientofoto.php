<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Cwmantenimientofoto extends Model
{
    protected $table = 'cwmantenimientofotos';

    protected $fillable = [
        'mantenimiento_id',
        'ruta_foto',
        'nombre_original',
        'tipo_evidencia',
        'descripcion',
        'orden'
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(CWMantenimiento::class, 'mantenimiento_id');
    }

    // Accesor para obtener URL completa
    /*public function getUrlAttribute()
    {
        return asset('storage/public/' . $this->ruta_foto);
    }*/

    public function getUrlAttribute()
    {
        return Storage::url($this->ruta_foto);
    }
}
