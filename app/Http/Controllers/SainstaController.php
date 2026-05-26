<?php

namespace App\Http\Controllers;

use App\Models\Sainsta;
use App\Models\Saprod;
use App\Models\Sasucursal;
use Illuminate\Http\Request;

class SainstaController extends Controller
{
    public function index()
    {
        $comercial  = session('comercialid') ;
        $instanciaspadre = Sainsta::where('comercial',$comercial)
            ->selectRaw(" CONCAT (repeat('&nbsp;',(Nivel)*4) , ' ' ,  Descrip) as label, descrip, id ")->orderBy('codalte')->get();
        return view('sub-categories',compact('instanciaspadre'));
    }

    public function json()
    {
        $comercial = session('comercialid');
        $all = Sainsta::where('comercial', $comercial)
            ->with(['padre', 'hijos', 'productos', 'servicios'])
            ->orderBy('descrip', 'asc')
            ->get();

        $instancias = [];
        foreach ($all as $item) {
            $instancias[] = [
                "id" => $item->id,
                "subcategory" => $item->descrip,
                "category" => $item->padre ? $item->padre->descrip : '',
                "hijos" => $item->hijos->count() > 0,
                "productos" => $item->productos->count() > 0,
                "servicios" => $item->servicios->count() > 0
            ];
        }

        return response()->json($instancias);
    }

    public function lastprod($codinst)
    {
        $last   = '';
        $comercial  = session('comercialid') ;
        $product = Saprod::where('comercial',$comercial)->orderBy('id','desc')->first();

        if(isset($product) and $product->codprod != '')
            $last = $product->codprod;

        return response()->json(['last' => $last ]);
    }

    public function list(Request $request)
    {
        $comercial  = $request->comercial;
        $comercial  = str_replace("4000","",$comercial);
        if(!$comercial or $comercial==0){
            $fk_sucursal = $request->sucursal;
            $fk_sucursal = str_replace("300","",$fk_sucursal);
            $sucursal    = Sasucursal::find($fk_sucursal);
            $comercial   = $sucursal->fk_comercial;
        }
        $instancias = Sainsta::where('comercial',$comercial)->orderBy('codinst','desc')->get();
        return response()->json(['success'=>'success', 'instancias' => $instancias], 200);
    }

    public function listComercial(Request $request)
    {
        $comercialid = str_replace("700","",$request->comercial);
        $instancias = Sainsta::where('comercial',$comercialid)->orderBy('codinst','desc')->get();
        return response()->json(['success'=>'success', 'instancias' => $instancias], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $comercial = session('comercialid');
        $insPadre = $request->insPadre;
        $nivel = 1;
        $padreid = 0;
        $codalte = '';

        if ($insPadre and $insPadre != '0' and $insPadre != 'Seleccione') {
            $padre = Sainsta::where(['comercial' => $comercial, 'descrip' => $insPadre])->first();
            if ($padre) {
                $nivel = $padre->nivel + 1;
                $padreid = $padre->codinst;
                $codalte = $padre->codalte;
            }
        }

        $new = new Sainsta();
        $new->descrip = strtoupper($request->descrip);
        $new->insPadre = $padreid;
        $new->nivel = $nivel;
        $new->comercial = $comercial;
        $new->codinst = 0;
        $new->codalte = '';
        $new->save();

        $new->codinst = $new->id;
        if (!$codalte) {
            $new->codalte = $new->id;
        } else {
            $new->codalte = $codalte . $new->id;
        }
        $new->save();

        return response()->json([
            'success' => true,
            'id' => $new->id,
            'subcategory' => $new->descrip,
            'category' => $insPadre ?? ''
        ]);
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
        $comercial = session('comercialid');
        $sainsta = Sainsta::find($id);

        if (!$sainsta) {
            return response()->json(['error' => 'Instancia no encontrada'], 404);
        }

        $insPadre = $request->insPadre;
        $codaltepadre = '';
        $padreid = 0;
        $nivel = 1;

        if ($insPadre and $insPadre != '0' and $insPadre != 'Seleccione') {
            $padre = Sainsta::where(['comercial' => $comercial, 'descrip' => $insPadre])->first();
            if ($padre) {
                $nivel = $padre->nivel + 1;
                $padreid = $padre->codinst;
                $codaltepadre = $padre->codalte;
            }
        }

        $sainsta->descrip = strtoupper($request->descrip);
        $sainsta->insPadre = $padreid;
        $sainsta->nivel = $nivel;

        if ($codaltepadre) {
            $sainsta->codalte = $codaltepadre . $sainsta->id;
        }

        $sainsta->save();

        return response()->json(['success' => true, 'id' => $sainsta->id]);
    }

    public function destroy($id)
    {
        $sainsta = Sainsta::find($id);
        if ($sainsta) {
            // Verificar si tiene hijos o productos antes de eliminar
            if ($sainsta->hijos->count() > 0 || $sainsta->productos->count() > 0) {
                return response()->json([
                    'error' => 'No se puede eliminar porque tiene elementos asociados'
                ], 400);
            }
            $sainsta->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'Instancia no encontrada'], 404);
    }
}
