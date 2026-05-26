<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sainsta extends Model
{
    use HasFactory;
    protected $table    = 'sainsta';
    protected $fillable = ['codinst', 'descrip', 'insPadre', 'nivel', 'tipoIns', 'DEsComp', 'codalte', 'desseri'];

    public function padre(){
        return $this->belongsTo(Sainsta::class, 'insPadre', 'codinst') ;
    }

    public function hijos  (){
        return $this->hasMany(Sainsta::class, 'insPadre', 'id') ;
    }

    public function productos  (){
        return $this->hasMany(Saprod::class, 'codinst', 'codinst');
    }

    public function productosexistencias  (){

        return $this->hasMany(Saprod::class, 'codinst', 'codinst')
            ->where('saprod.existen', '<>', 0)
            ->where('saprod.comercial',1);
    }

    public function servicios  (){
        return $this->hasMany(Saserv::class, 'codinst', 'codinst');
    }

    public function comercial  (){
        return $this->belongsTo(Sacomercial::class, 'comercial', 'id');
    }

    public function scopePorComercial($query, $comercialId)
    {
        return $query->where('comercial', $comercialId);
    }

    // Scope para instancias de nivel 1 (departamentos principales)
    public function scopeNivel1($query)
    {
        $comercial = session('comercialid') ;
        return $query->where('nivel', 1)->where('comercial',$comercial);
    }

    public function getFormattedDataAttribute()
    {
        return [
            'id'          => $this->id,
            'subcategory' => $this->descrip,
            'category'    => $this->padre ? $this->padre->descrip : '',
            'hijos'       => $this->hijos->count() > 0,
            'productos'   => $this->productos->count() > 0,
            'servicios'   => $this->servicios->count() > 0
        ];
    }
}
