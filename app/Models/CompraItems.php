<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraItems extends Model
{
    protected $table = 'compra_items';

    function compra(){
        return  $this->belongsTo('App\Models\Compra', 'fk_compra','id');
    }

    function producto(){
        return  $this->belongsTo('App\Models\Saprod', 'fk_producto','id');
    }
}
