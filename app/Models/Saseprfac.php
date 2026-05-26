<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saseprfac extends Model
{
    use HasFactory;
    protected $table    = 'saseprfac';
    protected $fillable = ['codsucu', 'tipofac', 'numerod', 'nrolinea', 'nrolineac', 'nroserial', 'coditem', 'codubic',  'tipo'];
}
