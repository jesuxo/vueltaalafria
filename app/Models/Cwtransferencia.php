<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Cwtransferencia extends Model
{
    use HasFactory;
    use Hashidable;

    protected $table    = 'cwtransferencia';
    protected $fillable = ['fecha', 'monto', 'observacion', 'numero', 'status', 'bs', 'pesos', 'dolares','fkbanco','fksucursal'];

    public function banco(){
        return $this->belongsTo(Cwbancos::class, 'fkbanco', 'id');
    }
    public function sucursal(){
        return $this->belongsTo(Sasucursal::class, 'fksucursal', 'id');
    }

    protected $appends = ['hashid', 'fechaformat', 'currency'];

    public function getRouteKeyName()
    {
        return 'hashid';
    }

    public function getHashidAttribute()
    {
        return Hashids::connection(Cwtransferencia::class)->encode($this->id);
    }

    public function getFechaformatAttribute(){
        $date = $this->fecha;
        if(isset($date)){
            list($y,$m,$d) = explode('-',$date);
            return "$d/$m/$y";
        }
    }

    public function getCurrencyAttribute(){
        if($this->bs)
            return 'Bs ';
        if($this->dolares)
            return '$ ';
        if($this->pesos)
            return 'COP ';
    }

}
