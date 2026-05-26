<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraItems;
use App\Models\Saprod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComprasController extends Controller
{
    public function index(Request $request)
    {
        $compras = Compra::with(['items', 'usuario'])->where('encurso', 1)->orderBy('id','desc');

        $busqueda = $request->busqueda;

        if(isset($busqueda)){
            $sql      = '';
            $vector   = explode(' ', $busqueda);

            foreach ($vector as $index => $item){
                $subtxt = $item;

                if(!isset($request->busqueda) and !$request->busqueda)
                    $subtxt = substr($subtxt,0,3);

                if($index > 0){
                    $sql .= " and ";
                }
                $sql .= " ( name like '%$subtxt%' or email like '%$subtxt%' ) ";

                if($index > 1)
                    break;
            }

            $compras  = $compras->whereHas('usuario',function ($query) use ($sql){
                $query->whereRaw("($sql)");
            });

            $compras  = $compras->orWhere('monto','like',"%$busqueda%");
            $idsearch = str_replace('#','',$busqueda);
            $compras  = $compras->orWhere('id',"$idsearch");

        }


        $compras = $compras->get()->take(30);

        $vista = view('dashboard.partials.compras', compact('compras','busqueda'))->render();

        return response()->json(['view' => $vista], 200);
    }

    public function abrirlista()
    {
        if(isset(auth()->user()->id)){

            $monto  = 0;
            $id     = auth()->id();
            $client = auth()->user()->name;
            $compra = Compra::with('items.producto')->where(['fk_user' => $id, 'encurso' => 1])->first();
            $count  = 0;
            $lista  = view('home.layouts.partials.listaproductos', compact('compra'))->render();

            if(isset($compra)){
                if(isset($compra->items[0])){
                    $count = count($compra->items);
                    foreach($compra->items as $item){
                       // if(isset($item->producto->existen)){
                           // if($item->producto->existen > 0)
                                $monto += $item->costod3 * $item->cantidad;
                       // }
                    }
                }

                $compra->monto = $monto;
                $compra->save();
            }

            $monto = "$ ".number_format($monto, 2, ',', '.');


            return response()->json([ 'lista' => $lista, 'count' => $count,  'monto' => $monto, 'href' => ''],200);
        }

        return response()->json(['login' => 1],200);
    }

    public function agregar(Request $request)
    {
        $id   = $request->id;
        $cant = $request->cant ;
        $max  = $request->max ;

        $producto = Saprod::find($id);

        if(auth()->id()){

                $user_id      = auth()->id();
                $current = Compra::where(['fk_user' => $user_id, 'encurso' => 1])->first();

                if(!isset($current->id)){
                     $current          = new Compra();
                     $current->fk_user = $user_id;
                     $current->save();
                }

                $item              = new CompraItems();
                $item->fk_compra   = $current->id;
                $item->cantidad    = $cant;
                $item->fk_producto = $producto->id;

                $precio = $producto->costod3;


                $item->precio = $precio;
                $item->save();

                $preview = view('home.layouts.partials.agregado', compact('producto', 'precio'))->render();

                $count  = 0;
                $compra = Compra::with('items')->where(['encurso' => 1, 'fk_user' => $user_id ])->first();

                if(isset($compra->items[0]))
                    $count = count($compra->items);

                return response()->json(['added' => 1, 'preview' => $preview, 'count' => $count],200);

        }

        return response()->json(['login' => 1],200);
    }

    public function edit(Compra $compra)
    {
        return view('home.layouts.partials.listaproductos', compact( 'compra'))->render();
    }
}
