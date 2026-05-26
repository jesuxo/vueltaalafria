<?php
// app/Models/CWProductoRecomendado.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CWProductoRecomendado extends Model
{
    protected $table = 'cwproductosrecomendados';
    protected $fillable = [
        'fk_vehiculo', 'tipo_producto', 'producto_recomendado',
        'especificacion', 'notas'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(CWVehiculo::class, 'fk_vehiculo');
    }
}
