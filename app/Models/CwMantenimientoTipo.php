<?php
// app/Models/CwMantenimientoTipo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CwMantenimientoTipo extends Model
{
    protected $table = 'cwmantenimiento_tipos';

    protected $fillable = [
        'mantenimiento_id', 'tipo', 'descripcion'
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(CWMantenimiento::class, 'mantenimiento_id');
    }

    public function getTipoTextoAttribute()
    {
        $tipos = [
            'cambio_aceite' => 'Cambio de Aceite',
            'cambio_filtro_aceite' => 'Filtro de Aceite',
            'cambio_filtro_gasolina' => 'Filtro de Gasolina',
            'cambio_filtro_aire' => 'Filtro de Aire',
            'mantenimiento_inyectores' => 'Inyectores',
            'bateria' => 'Batería',
            'otros' => 'Otros'
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }
}
