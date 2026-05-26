<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cwmantenimientoproducto extends Model
{
    protected $table = 'cwmantenimientoproductos';

    protected $fillable = [
        'mantenimiento_id', 'codprod', 'descripcion', 'referencia',
        'cantidad', 'precio', 'tipo'
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(CWMantenimiento::class, 'mantenimiento_id');
    }

    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'codprod', 'codprod');
    }
}
