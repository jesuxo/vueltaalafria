<?php

namespace App\Http\Controllers;

use App\Models\Saacxc;
use App\Models\Saipacxc;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SaacxcController extends Controller
{
    public function index()
    {
        //
    }

    public function saacxc($id = null)
    {
        if(!isset($id))
            $id = '';

        $comercial  = session('comercialid') ;

        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->orderBy('descrip')->get();

        $sucursalselected = '';
        foreach ($sucursales as $sucursal){
            if($sucursal->id == $id){
                $sucursalselected = $sucursal;
                break;
            }
        }

        return view('saacxc', compact('sucursales', 'sucursalselected', 'comercial') );
    }

    public function cxclist(Request $request)
    {
        $codclie = $request->codclie;
        return view('saacxclistado', compact('codclie'))->render();
    }

    public function cuentaxcobrar(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $cuentasporcobrar = $request->cuentasporcobrar;
        $cuentasporcobrar = json_decode($cuentasporcobrar);

        if(isset($cuentasporcobrar)){
            foreach ($cuentasporcobrar as $cxc){

                if(isset($cxc->NroUnico)){
                    $record = Saacxc::where(['NroUnico'=>  $cxc->NroUnico, 'fk_sucursal'=> $cxc->fk_sucursal])->first();

                    if(!isset($record->id)) {
                        $record = new Saacxc();
                        if( isset($cxc->tarjetas)){
                            foreach ($cxc->tarjetas as $tarjeta){
                                $newtar  = new Saipacxc();
                                $auxitem = (array) $tarjeta;
                                $newtar->fill($auxitem);
                                $newtar->save();
                            }
                        }
                    }

                    $aux = (array) $cxc;
                    $record->fill($aux) ;

                    $record->fk_sucursal = $cxc->fk_sucursal;

                    $record->save();
                }
            }
        }

        return response()->json(['success' => 'success', 'updated' => 1], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Saacxc $saacxc)
    {
        //
    }

    public function edit(Saacxc $saacxc)
    {
        //
    }

    public function update(Request $request, Saacxc $saacxc)
    {
        //
    }

    public function destroy(Saacxc $saacxc)
    {
        //
    }
}
