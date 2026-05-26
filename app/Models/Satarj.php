<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satarj extends Model
{
    use HasFactory;
    protected $table    = 'satarj';
    protected $fillable = ['codtarj', 'descrip', 'bs','dolares','pesos'];
}
