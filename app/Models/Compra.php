<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compra';

    function items(){
        return  $this->hasMany('App\Models\CompraItems', 'fk_compra','id');
    }

    function usuario(){
        return  $this->belongsTo('App\Models\User', 'fk_user','id');
    }
}
