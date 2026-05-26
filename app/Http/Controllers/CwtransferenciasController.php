<?php

namespace App\Http\Controllers;

use App\Models\Cwbancos;
use App\Models\Cwtransferencia;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class CwtransferenciasController extends Controller
{
    public function index(Request $request)
    {
        return  response()->redirectTo('index');
    }

    public function json($busquedatransf, $status, $fechas)
    {
        $transferencias = [];
        return response()->json($transferencias);
    }

    public function list(Request $request)
    {

    }

    public function reportetransferencias(Request $request)
    {
        $arraysucursales = auth()->user()->sucursales->pluck('fk_sucursal')->toArray();

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $selectbanco    = (isset($request->selectbanco))? $request->selectbanco : 0;
        $status         = (isset($request->status) and $request->status !='')? $request->status : '';
        $fechasreport   = $request->fechasreport;
        $busquedatransf = (isset( $request->busquedatransf))? $request->busquedatransf : '';

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = $fecha1 = $fecha2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
            if($fechasreport !='') {
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = "$d1/$m1/$y1";
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 !=''){
        $fecha1 = $fec1;
        $fecha2 = $fec2;

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        }

        $transferencias = [];

        if(($fec1 =='' and $fec2 =='') and $busquedatransf =='' and  $status == ''){
            $status = 0;
        }

        if(($fec1 !='' and $fec2 !='') or $busquedatransf !='' or $status >= 0){

            $transferencias = Cwtransferencia::query()
                ->orderByDesc('id')
                ->whereIn('fksucursal', $arraysucursales)
                ->with([
                    'sucursal', 'banco'
                ]);

            if($fec1 !='' and $fec2 !='') {
                $transferencias =  $transferencias->whereBetween('created_at', ["$fec1 00:00:00.00", "$fec2 23:59:59.00"]);
            }

            $cadena = '';
            if($busquedatransf) {

                $busquedatransf = str_replace('*',' ',$busquedatransf);
                $vector = explode(" ",$busquedatransf);
                $i=0;
                foreach ($vector as $item){
                    if($i>0)
                        $cadena .=" and ";
                    $cadena .=" (
                      numero like '%$item%'
                      or observacion like '%$item%'
                      or titular like '%$item%'
                      or monto like '%$item%'
                      or date_format(fecha,'%d/%m%Y')='$item'
                      or fkbanco in (select id from cwbancos where descrip like '%$item%')
                       )";
                    $i++;
                }
                if($cadena!='')
                    $transferencias = $transferencias->whereRaw(" ( $cadena ) ");


            }

            if($selectbanco > 0)
                $transferencias = $transferencias->where('fkbanco',  $selectbanco);

            if(isset($status) and $status >= 0)
                $transferencias = $transferencias->where('status',  $status)->limit(150);

            $transferencias = $transferencias->get();

        }

        $arraytransf    = [];
        $arraybstransf  = [];
        $arrayusdtransf = [];
        $arraysucu      = [];
        $arraycoptransf = [];
        $pendientes     = [];
        $aprobadas      = [];
        $rechazadas     = [];
        $bancos         = [];
        $sucursales     = [];

        if(isset($transferencias) and count($transferencias) > 0){
            foreach ($transferencias as $transf){
                array_push($arraytransf,$transf);

                if(!isset($sucursales[$transf->fksucursal]))   $sucursales[$transf->fksucursal] = $transf->sucursal->descrip;

                if($transf->status  == 0) array_push($pendientes, $transf);
                if($transf->status  == 1) array_push($aprobadas, $transf);
                if($transf->status  == 2) array_push($rechazadas, $transf);
                if($transf->bs      == 1) array_push($arraybstransf, $transf);
                if($transf->dolares == 1) array_push($arrayusdtransf, $transf);
                if($transf->cop     == 1) array_push($arraycoptransf, $transf);
                if(!isset($bancos[$transf->fkbanco])) $bancos[$transf->fkbanco] = [];

                if(!isset($bancos[$transf->fkbanco]['cant'])) $bancos[$transf->fkbanco]['cant'] = 0;
                if(!isset($arraysucu[$transf->fksucursal]['cant'])) $arraysucu[$transf->fksucursal]['cant'] = 0;
                if(!isset($bancos[$transf->fkbanco]['descrip'])) $bancos[$transf->fkbanco]['descrip'] = $transf->banco->descrip;

                $arraysucu[$transf->fksucursal]['cant'] = $arraysucu[$transf->fksucursal]['cant'] + 1;
                $bancos[$transf->fkbanco]['cant']       = $bancos[$transf->fkbanco]['cant'] + 1;

            }
        }

        return view('reporteTransferencias', compact( 'fechasreport','bancos','status',
            'arraybstransf', 'arraycoptransf', 'arrayusdtransf',  'arraytransf', 'pendientes', 'aprobadas', 'rechazadas', 'arraysucu',
            'sucursales',   'transferencias', 'fecha1', 'fecha2', 'selectbanco', 'busquedatransf'));

    }

    public function validar( $id)
    {
        if($id!='') {
            $hashid = Hashids::connection(Cwtransferencia::class)->decode($id)[0];
            $transf = Cwtransferencia::with('banco')->find($hashid);
            if(isset($transf)){
                if($transf->status < 1)
                    return view('transferencias-validar',compact('transf'));
                else
                    return view('transferencias-validada',compact('transf'));
            }else{
                return redirect()->route('login');
            }
        }else{
            return redirect()->route('login');
        }
    }

    public function cambiarstatus(Request $request)
    {
        $id = $request->id;
        $va = $request->va;

        $hashid = Hashids::connection(Cwtransferencia::class)->decode($id)[0];
        $transf = Cwtransferencia::find($hashid);
        if($transf->status < 1){
            $transf->status = $va;
            $transf->save();
            return response()->json(['cambiado' => 1 ]);
        }else{

            return response()->json(['cambiado' => 0 ]);
        }

    }

    public function create()
    {
        $arraysucursales = auth()->user()->sucursales->pluck('fk_sucursal')->toArray();
        $sucursales = Sasucursal::whereIn('id', $arraysucursales)->orderBy('descrip','asc')->get();
        return view('transferencias-create', compact('sucursales') );
    }

    public function store(Request $request)
    {
        $fecha = $request->fecha;
        list($d,$m,$y)=explode('/',$fecha);

        $bancosel = Cwbancos::find($request->bancosucursal);

        $busqueda = Cwtransferencia::where(['numero'=>$request->numero]);

        if(isset($bancosel->bs) and $bancosel->bs == 1)
            $busqueda->where(['bs'=>1]);
        if(isset($bancosel->pesos) and $bancosel->pesos == 1)
            $busqueda->where('pesos',1);
        if(isset($bancosel->dolares) and $bancosel->dolares == 1)
            $busqueda->where('dolares',1);

        $busqueda = $busqueda->first();
        $valid = 1;

        if(isset($busqueda) and $busqueda->numero){
            $valid = 0;
        }

        if($valid){
            $new = new Cwtransferencia();

            $new->fecha       = "$y-$m-$d";
            $new->numero      = strtoupper($request->numero);
            $new->observacion = strtoupper($request->observacion);
            $new->titular     = strtoupper($request->titular);
            $new->monto       = $request->monto;
            $new->fkbanco     = $request->bancosucursal;
            $new->fksucursal  = $request->fksucursal;
            if(isset($bancosel->bs)      and $bancosel->bs      == 1)
                $new->bs = 1;
            if(isset($bancosel->pesos)   and $bancosel->pesos   == 1)
                $new->pesos = 1;
            if(isset($bancosel->dolares) and $bancosel->dolares == 1)
                $new->dolares = 1;

            $new->save();
        }

        return redirect()->route('reportetransferencias');
    }

    public function verificar(Request $request)
    {
        $fecha = $request->fecha;
        list($d,$m,$y)=explode('/',$fecha);

        //, 'fkbanco'=>$request->fkbanco, 'fecha'=>"$y-$m-$d"

        $busqueda = Cwtransferencia::where(['numero'=>$request->numero,'monto'=> $request->monto])->limit(40);

        $busqueda = $busqueda->first();
        $valid = 1;

        if(isset($busqueda) and $busqueda->numero){
            $valid = 0;
        }

        return response()->json(['valid' => $valid ]);

    }

    public function filtrarstatus(Request $request, $status)
    {
        $fechas         = ($request->fechas)        ? $request->fechas         :'';
        $busquedatransf = ($request->busquedatransf)? $request->busquedatransf :'';
        $status         = ($request->status>=0)     ? $request->status         :'';

        return view('transferencias',compact('status','fechas', 'busquedatransf') );
    }

    public function informacion(Request $request)
    {
        $urlencode = '';
        $options = $request->options;
        if(!$options) {
            $options = 0;
        }else{
            list($id,$fksucursal)=explode("-", $options);
            $banco = Cwbancos::where(['id'=>$id,'fksucursal'=>$fksucursal])->first();
            $urlencode = urlencode($banco->texto);
        }
        $telefono= $request->telefono;
        $error   = 0;
        $listado = Cwbancos::whereRaw("texto <> ''")->get();
        if (!preg_match('/^\+?[0-9]{12}$/i', $telefono)) {
           $error = 1;
        }
        return view('transf-send',compact('listado','telefono', 'error', 'options', 'urlencode') );
    }

    public function apiStatus(Request $request)
    {
        $array = $request->array;
        $array = json_decode($array);
        $descargadas = 0;

        if (isset($array))
            foreach ($array as $item){

                $aux = Cwtransferencia::find($item->id);
                $aux->descargada = 1;
                $aux->save();
                $descargadas ++;

            }
        if ($descargadas > 0)
            return response()->json(['success'=>'success']);
        else
            return response()->json(['error'=>'1']);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $transf = Cwtransferencia::find($id);
        try {
            $transf->delete();
            return response()->json(['deleted' => 1 ]);
        }catch (\Exception $e){
            return response()->json(['deleted' => 0 ]);
        }

    }
}
