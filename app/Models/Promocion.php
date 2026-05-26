<?php

namespace App\Models;;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promocion';

    function producto(){
        return  $this->belongsTo(Saprod::class, 'codprod','saint');
    }

}
