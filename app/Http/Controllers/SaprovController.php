<?php

namespace App\Http\Controllers;

use App\Models\Saprov;
use App\Models\Saprovsucursal;
use App\Models\Saprod;
use App\Models\User;
use Illuminate\Http\Request;

class SaprovController extends Controller
{

    public function index()
    {

        $user       = User::where('id',auth()->user()->id)->with(['sucursales.sucursal.saprovsucursales.proveedor'])->first();
        $proveedores= [];
        $sucursales = [];

        foreach ($user->sucursales as $rel){
            array_push($sucursales, $rel->sucursal);
            if(isset($rel->sucursal->saprovsucursales)){
                foreach ($rel->sucursal->saprovsucursales as $relproveedor){
                    array_push($proveedores, $relproveedor->proveedor);
                }
            }
        }

        return view('proveedores-list',compact('proveedores'));
    }

    public function json()
    {
        $user     = User::where('id',auth()->user()->id)->with(['sucursales.sucursal.saprovsucursales.proveedor'])->first();
        $proveedores = $sucursales = $aux = $all = [];
        foreach ($user->sucursales as $rel){
            array_push($sucursales, $rel->sucursal);
            if(isset($rel->sucursal->Saprovsucursales)){
                foreach ($rel->sucursal->Saprovsucursales as $relproveedor){
                    array_push($proveedores, $relproveedor->proveedor);
                }
            }
        }

        foreach ($proveedores as $item){
            list($fecha,$hora) = explode(" ",$item->created_at);
            list($y,$m,$d) = explode("-",$fecha);
            $aux = [
                "id"        => "$item->id",
                "codprov"   => "$item->codprov",
                "descrip"   => "$item->descrip",
                "telef"     => "$item->telef"." "."$item->movil",
                "date"      => "$y-$m-$d",
                "datelabel" => "$d/$m/$y"
            ];

            array_push($all, $aux);
        }
        return response()->json($all);

    }

    public function saprovsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $proveedores = $request->proveedores;
        $proveedores = json_decode($proveedores);

        if (isset($proveedores))
            foreach ($proveedores as $proveedor){
                $aux = Saprovsucursal::where(['codprov' => $proveedor->codprov, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprovsucursal();
                    $rel->codprov     = $proveedor->codprov;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $proveedores   = $request->proveedores;
        $proveedores   = json_decode($proveedores);

        if(isset($proveedores))
            foreach ($proveedores as $proveedor){

                $aux = Saprov::where(['codprov' => $proveedor->codprov])->first();
                if(!$aux){
                    $new = new Saprov();


                    $new->id3           = ($proveedor->id3)       ?$proveedor->id3        : '';
                    $new->fax           = ($proveedor->fax)       ?$proveedor->fax        : '';
                    $new->clase         = ($proveedor->clase)     ?$proveedor->clase      : '';
                    $new->telef         = ($proveedor->telef)     ?$proveedor->telef      : '';
                    $new->movil         = ($proveedor->movil)     ?$proveedor->movil      : '';
                    $new->email         = ($proveedor->email)     ?$proveedor->email      : '';
                    $new->direc1        = ($proveedor->direc1)    ?$proveedor->direc1     : '';
                    $new->direc2        = ($proveedor->direc2)    ?$proveedor->direc2     : '';
                    $new->activo        = ($proveedor->activo)    ?$proveedor->activo     : 0;
                    $new->codprov       = $proveedor->codprov;
                    $new->tipoprv       = ($proveedor->tipoprv)   ?$proveedor->tipoprv    : 0;
                    $new->tipoid3       = ($proveedor->tipoid3)   ?$proveedor->tipoid3    : 0;
                    $new->tipoid        = ($proveedor->tipoid)    ?$proveedor->tipoid     : 0;
                    $new->descrip       = ($proveedor->descrip)   ?$proveedor->descrip    : '';
                    $new->represent     = ($proveedor->represent) ?$proveedor->represent  : '';
                    $new->zipcode       = ($proveedor->zipcode)   ?$proveedor->zipcode    : '';
                    $new->blockdesc     = ($proveedor->blockdesc) ?$proveedor->blockdesc  : 0;
                    $new->observa       = ($proveedor->observa)   ?$proveedor->observa    : '';

                    $new->save();

                    $rel              = new Saprovsucursal();
                    $rel->codprov     = $proveedor->codprov;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }else{
                    $aux = Saprovsucursal::where(['codprov' => $proveedor->codprov, 'fk_sucursal'=>$sucursalid])->first();
                    if(!$aux){
                        $rel              = new Saprovsucursal();
                        $rel->codprov     = $proveedor->codprov;
                        $rel->fk_sucursal = $sucursalid;
                        $rel->save();
                    }
                }
            }

        $proveedores = Saprov::whereRaw("codprov not in (select codprov from saprovsucursal where fk_sucursal=$sucursalid )")->get();


        return response()->json(['success'=>'success', 'newproveedores' => $proveedores]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
        //
    }
}
