<?php

namespace App\Http\Controllers;

use App\Models\Saipacxc;
use App\Models\Saipavta;
use App\Models\Sasucursal;
use App\Models\Satarj;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SatarjController extends Controller
{
    public function instpagobs(Request $request)
    {
        $comercialid  = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        }else{
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $montos = Saipavta::selectRaw(" fk_sucursal, codpago,
                        sum(case tipofac
                               when 'A' then (monto)
                               when 'B' then (monto * -1)
                               else 0
                            end
                        )as bs")
                        ->with(['sucursal.comercial', 'satarj'])
                        ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                            $q->where('fk_comercial', $comercialid);
                        })
                        ->whereHas('satarj', function($q) {
                            $q->where('bs', 1);
                        })
                        ->whereBetween('fechae', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00'])
                        ->groupBy(["fk_sucursal", "codpago", "tipofac"])
                        ->get();

        $clases     = [];
        $sucursales = [];
        $listado    = [];

        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($sucursales[$monto->sucursal->descrip][$monto->satarj->descrip])){
                    $sucursales[$monto->sucursal->descrip][$monto->satarj->descrip] = $monto->sucursal->descrip;
                }

                if(!isset($clases[$monto->satarj->clase])){
                    $clases[$monto->satarj->clase] = $monto->satarj->codtarj;
                }

                if(!isset($listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase]))
                    $listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase] = 0;

                $listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase] += $monto->bs;

            }


          $montos = Saipacxc::selectRaw(" fk_sucursal, codpago, sum(monto)as bs")
            ->with(['sucursal.comercial', 'satarj', 'cxc'])
            ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->whereHas('satarj', function($q) {
                $q->where('bs', 1);
            })
            ->whereHas('cxc', function($q) use ($fec1,$fec2) {
                $q->whereBetween('fechae', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00']);
            })
            ->groupBy(["fk_sucursal", "codpago"])
            ->get();

        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($sucursales[$monto->sucursal->descrip][$monto->satarj->descrip])){
                    $sucursales[$monto->sucursal->descrip][$monto->satarj->descrip] = $monto->sucursal->descrip;
                }

                if(!isset($clases[$monto->satarj->clase])){
                    $clases[$monto->satarj->clase] = $monto->satarj->codtarj;
                }

                if(!isset($listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase]))
                    $listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase] = 0;

                $listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase] += $monto->bs;

            }


        ksort($sucursales);
        ksort($clases);

        return view('reporteInstPago', compact('fechasreport','clases', 'sucursales', 'fecha1', 'fecha2', 'listado'));
    }

    public function instpagodolares(Request $request)
    {
        $comercialid  = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $fechasreport = $request->fechasreport;
        $fechashoy    =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        }else{
            if(!$nofilterdate) {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }else{
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $montos = Saipavta::selectRaw(" fk_sucursal, codpago,
                        sum(case tipofac
                               when 'A' then (dolares)
                               when 'B' then (dolares * -1)
                               else 0
                            end
                        )as dolares")
            ->with(['sucursal.comercial', 'satarj'])
            ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->whereHas('satarj', function($q) {
                $q->where('dolares', 1);
            })
            ->whereBetween('fechae', [$fec1.' 00:00:00.00', $fec2.' 23:58:22.00'])
            ->groupBy(["fk_sucursal", "codpago", "tipofac"])
            ->get();

        $clases     = [];
        $sucursales = [];
        $listado    = [];

        if(isset($montos))
            foreach ($montos as $monto) {

                if(!isset($sucursales[$monto->sucursal->descrip][$monto->satarj->descrip])){
                    $sucursales[$monto->sucursal->descrip][$monto->satarj->descrip] = $monto->sucursal->descrip;
                }

                if(!isset($clases[$monto->satarj->clase])){
                    $clases[$monto->satarj->clase] = $monto->satarj->codtarj;
                }

                if(!isset($listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase]))
                    $listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase] = 0;

                $listado[$monto->sucursal->descrip][$monto->satarj->descrip][$monto->satarj->clase] += $monto->dolares;

            }

        ksort($sucursales);
        ksort($clases);

        return view('reporteInstPagodolares', compact('fechasreport','clases', 'sucursales', 'fecha1', 'fecha2', 'listado'));
    }

    public function index()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $tarjetas  = Satarj::where('comercial',$comercialid)->get();
        return view('tarjetas-list-view', compact('tarjetas') );
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercialid  = $sucursal->fk_comercial;

        $tarjetas  = Satarj::where('comercial',$comercialid)->get();
        return response()->json(['success'=>'success', 'tarjetas' => $tarjetas], 200);
    }

    public function json()
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $all = Satarj::where('comercial',$comercialid)->orderBy('descrip','asc')->get();
        $aux = [];
        $tajretass = [];
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "codtarj"       => "$item->codtarj",
                "descrip"       => "$item->descrip",
                "bs"            => ($item->bs       == 1)? "1": "0",
                "dolares"       => ($item->dolares  == 1)? "1": "0",
                "pesos"         => ($item->pesos    == 1)? "1": "0",
                "activo"        => ($item->activo)? "Activo":"Inactivo"
            ];
            array_push($tajretass,$aux);
        }
        return response()->json($tajretass );
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }
        $tarjeta = Satarj::where(['codtarj' => $request->codtarj, 'comercial' => $comercialid])->first();

        if(!isset($tarjeta->codubic)){
            $newTarj = new Satarj();
            $newTarj->fill($request->all());
            $newTarj->bs      = ($request->bs)     ? 1: 0;
            $newTarj->pesos   = ($request->pesos)  ? 1: 0;
            $newTarj->dolares = ($request->dolares)? 1: 0;
            $newTarj->activo  = 1;
            $newTarj->comercial  = $comercialid;
            $newTarj->save();
            return response()->json(['success'=>'success' ]);
        }else{
            return response()->json(['error'=>'error' ]);
        }


    }

    public function show(Satarj $Satarj)
    {
        //
    }

    public function edit(Satarj $Satarj)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $Instpago = Satarj::find($id);
        $Instpago->descrip   = $request->descrip;
        $Instpago->bs        = ($request->bs           == 1 )? 1: 0;
        $Instpago->dolares   = ($request->dolares      == 1 )? 1: 0;
        $Instpago->pesos     = ($request->pesos        == 1 )? 1: 0;
        $Instpago->activo    = ($request->activo       == 'Activo')? 1: 0;
        $Instpago->save();

        response()->json(['success'=>'success',"actializado"=>111]);
    }

    public function destroy(Satarj $Satarj)
    {
        //
    }
}
