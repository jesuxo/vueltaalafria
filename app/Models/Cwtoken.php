<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cwtoken extends Model
{
    use HasFactory;
    protected $table    = 'cwtoken';
    protected $fillable = ['id', 'token', 'codusua', 'status'];

    protected $appends = [ 'fechaformat'];


    public function getFechaformatAttribute(){
        $date = $this->created_at;
        if(isset($date)){
            list($date,$time) = explode(' ',$date);
            list($y,$m,$d) = explode('-',$date);
            return "$d/$m/$y";
        }
    }

    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fksucursal', 'id');
    }

}
