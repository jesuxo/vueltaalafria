<?php

namespace App\Http\Controllers;

use App\Models\CWVehiculo;
use Illuminate\Http\Request;

class CWVehiculoController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $codclie = $request->codclie;

        $new = new CWVehiculo();
        $new->fill($request->all());
        $new->save();
        return response()->redirectTo("/clientes/$codclie/tab3");
    }

    public function show(CWVehiculo $CWVehiculo)
    {
        //
    }

    public function edit(CWVehiculo $CWVehiculo)
    {
        //
    }

    public function update(Request $request, CWVehiculo $CWVehiculo)
    {
        //
    }

    public function destroy(CWVehiculo $CWVehiculo)
    {
        //
    }
}
