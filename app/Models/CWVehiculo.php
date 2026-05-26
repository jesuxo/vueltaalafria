<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\VehiculoFotoHelper;

class CWVehiculo extends Model
{
    use HasFactory;
    protected $table    = 'cwvehiculo';
    protected $fillable = ['codclie', 'fk_tipo', 'modelo', 'marca', 'identificacion',
        'year', 'observaciones', 'serialchasis', 'serialmotor', 'foto_vehiculo'];

    public function cliente  (){
        return $this->hasOne(Saclie::class, 'codclie', 'codclie');
    }

    public function tipo  (){
        return $this->hasOne(CWTipoVehiculo::class, 'id', 'fk_tipo');
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('d/m/Y');
    }


    // NUEVAS RELACIONES
    public function mantenimientos()
    {
        return $this->hasMany(CWMantenimiento::class, 'fk_vehiculo');
    }

    public function productosRecomendados()
    {
        return $this->hasMany(CWProductoRecomendado::class, 'fk_vehiculo');
    }

    // Obtener el último mantenimiento
    public function ultimoMantenimiento()
    {
        return $this->hasOne(CWMantenimiento::class, 'fk_vehiculo')->latest('fecha_mantenimiento');
    }

    // Obtener el próximo mantenimiento recomendado
    public function proximoMantenimiento()
    {
        return $this->mantenimientos()
            ->whereNotNull('proximo_mantenimiento')
            ->orWhereNotNull('proximo_kilometraje')
            ->latest('fecha_mantenimiento');
    }



// Accesor para URL de foto principal
    public function getFotoUrlAttribute()
    {
        return VehiculoFotoHelper::getUrl($this->foto_vehiculo);
    }

// Método para obtener todas las fotos
    public function getTodasLasFotos()
    {
        return VehiculoFotoHelper::listarFotos($this->id);
    }
}
