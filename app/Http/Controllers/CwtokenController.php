<?php

namespace App\Http\Controllers;

use App\Models\Cwbancos;
use App\Models\Cwtoken;
use App\Models\Cwtokenfailed;
use App\Models\Cwtransferencia;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class CwtokenController extends Controller
{
    public function index(Request $request)
    {
        dd('index');
    }

    public function reportetokens(Request $request)
    {
        if(Auth::user()  and auth()->user()->can('menu_token') ){

            $busquedatoken = (isset( $request->busquedatoken))? $request->busquedatoken : '';

            $tokens = Cwtoken::query()
                ->with('sucursal')
                ->orderByDesc('id');

            $cadena = '';
            if($busquedatoken) {

                $busquedatoken = str_replace('*',' ',$busquedatoken);
                $vector = explode(" ",$busquedatoken);
                $i=0;
                foreach ($vector as $item){
                    if($i>0)
                        $cadena .=" and ";
                    $cadena .=" (
                              token like '%$item%'  or codusua like '%$item%' or obs like '%$item%'
                           )";
                    $i++;
                }
                if($cadena!='')
                    $tokens = $tokens->whereRaw(" ( $cadena ) ");

                $tokens = $tokens->limit(50);

            } else{
                $tokens = $tokens->limit(100);
            }

            $tokens = $tokens->get();

            $arraytokens    = [];
            $pendientes     = [];
            $usados         = [];

            if(isset($tokens) and count($tokens) > 0){
                foreach ($tokens as $token){
                    array_push($arraytokens, $token);
                    if($token->status  == 0) array_push($pendientes, $token);
                    if($token->status  == 1) array_push($usados, $token);
                }
            }

            return view('reporteTokens',
                compact(    'arraytokens', 'pendientes', 'usados', 'tokens', 'busquedatoken'));
        }else{
            return response()->redirectTo('index');
        }
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        $token    = (isset($request->token))? $request->token : '';
        $token    = str_replace(' ','',$token);
        $token    = trim($token);

        $busqueda = Cwtoken::where(['token' => $token])->first();
        $valid = 1;

        if(isset($busqueda) and $busqueda->token){
            $valid = 0;
        }

        if($valid){
            $new        = new Cwtoken();
            $new->token = strtoupper($token);
            $new->save();
        }

        return redirect()->route('reportetokens')->with('valid', $valid);
    }

    public function apiCheck(Request $request)
    {
        $sucursalid   = str_replace("300","",$request->sucursal);
        $sucursal     = Sasucursal::find($sucursalid);

        $token   = $request->token;
        $codusua = $request->codusua;

        $aux = Cwtoken::where(['token'=> $token, 'status' => 0])->first();

        if (isset($aux) and isset($aux->status) and $aux->status == 0){
            $aux->status  = 1;
            $aux->codusua = $codusua;
            $aux->save();
            return response()->json(['success'=>'success']);
        }else{
            if(isset($aux) and isset($aux->status) and $aux->status == 1)
                    $token = $token.'-****';

            $aux              = new Cwtokenfailed();
            $aux->tokenfailed = $token;
            $aux->codusua     = $codusua;
            $aux->save();
            return response()->json(['error'=>'1']);
        }
    }


    public function newtoken(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);

        $obs     = $request->obs;
        $codusua = $request->codusua;

        $aux = new Cwtoken();
        $aux->token      = '';
        $aux->codusua    = strtoupper($codusua);
        $aux->obs        = strtoupper($obs);
        $aux->fksucursal = $sucursalid;
        $aux->save();

        return response()->json(['success'=>'success']);


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

    }

    public function tokenupdate(Request $request)
    {
        $token    = (isset($request->token))? $request->token : '';
        $tokenid  = (isset($request->tokenid))? $request->tokenid : 0;
        $token    = str_replace(' ','',$token);
        $token    = trim($token);

        $busqueda = Cwtoken::where(['token' => $token,'status'=>0])->first();
        $valid = 1;

        if(isset($busqueda) and $busqueda->token){
            $valid = 0;
            return redirect()->route('reportetokens')->with('valid', $valid);
        }

        $tokenobj = Cwtoken::find($tokenid);
        $tokenobj->token = strtoupper($token);
        $tokenobj->save();

        return redirect()->route('reportetokens')->with('valid', $valid);

    }

    public function destroy($id)
    {
        $token = Cwtoken::find($id);
        try {
            $token->delete();
            return response()->json(['deleted' => 1 ]);
        }catch (\Exception $e){
            return response()->json(['deleted' => 0 ]);
        }

    }
}
