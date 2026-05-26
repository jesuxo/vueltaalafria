<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CWTipoVehiculo extends Model
{
    use HasFactory;
    protected $table    = 'cwtipovehiculo';
    protected $fillable = ['id', 'tipo', 'activo'];

    public function vehiculos  (){
        return $this->hasMany(CWVehiculo::class, 'fk_tipo', 'id');
    }
}
