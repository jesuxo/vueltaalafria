<?php

namespace App\Http\Controllers;

use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SasucursalController extends Controller
{
    public function store(Request $request)
    {
        $comercial  = $request->comercial;
        $comercial  = str_replace("4000","",$comercial);

        $new = new Sasucursal();
        $new->fill($request->all());
        $new->fk_comercial = $comercial;
        $new->save();
        $lastid = $new->id;
        return response()->json(['id' => $lastid]);
    }


}
