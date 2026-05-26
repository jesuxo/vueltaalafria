<?php

namespace App\Http\Controllers;

use App\Exports\SaprodExport;
use App\Imports\SaprodUpdate;
use App\Models\Saexis;
use App\Models\Sainsta;
use App\Models\Saitemfac;
use App\Models\Saprod;
use App\Models\SaprodImagen;
use App\Models\Saprodsucursal;
use App\Models\Saserv;
use App\Models\Sasucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Intervention\Image\ImageManager;
use Maatwebsite\Excel\Facades\Excel;

class SaprodController extends Controller
{
    private function corregirOrientacionImagen($imagen, $rutaOriginal)
    {
        if (!function_exists('exif_read_data')) {
            return $imagen;
        }

        try {
            $exif = @exif_read_data($rutaOriginal);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];

                switch ($orientation) {
                    case 3:
                        $imagen->rotate(180);
                        break;
                    case 6:
                        $imagen->rotate(-90);
                        break;
                    case 8:
                        $imagen->rotate(90);
                        break;
                    case 2:
                        $imagen->flip('h');
                        break;
                    case 4:
                        $imagen->flip('v');
                        break;
                    case 5:
                        $imagen->flip('h');
                        $imagen->rotate(90);
                        break;
                    case 7:
                        $imagen->flip('h');
                        $imagen->rotate(-90);
                        break;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error al leer EXIF: ' . $e->getMessage());
        }

        return $imagen;
    }

    /**
     * Guardar una imagen de producto con compresión
     */
    private function guardarImagenProducto($archivo, $producto, $esPrincipal = false, $orden = 0)
    {
        $comercial = session('comercialid', 1);

        // Crear directorio: productos_comercial_{comercial}/producto_{id}/
        $directorio = public_path("productosimg/comercial_{$comercial}/producto_{$producto->id}");
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $nombreOriginal = $archivo->getClientOriginalName();
        $nombreArchivo = ($esPrincipal ? 'principal_' : 'img_') . time() . '_' . uniqid() . '.jpg';
        $rutaRelativa = "productosimg/comercial_{$comercial}/producto_{$producto->id}/{$nombreArchivo}";
        $rutaCompleta = public_path($rutaRelativa);

        try {
            $manager = new ImageManager();
            $imagen = $manager->make($archivo->getPathname());
            $imagen = $this->corregirOrientacionImagen($imagen, $archivo->getPathname());

            // Redimensionar si es muy grande (max 1200px)
            if ($imagen->width() > 1200) {
                $imagen->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Comprimir y guardar como JPG con calidad 75%
            $imagen->save($rutaCompleta, 75);

            // Guardar en BD
            return SaprodImagen::create([
                'producto_id' => $producto->id,
                'ruta_imagen' => $rutaRelativa,
                'es_principal' => $esPrincipal,
                'orden' => $orden,
                'nombre_original' => $nombreOriginal,
                'comercial_id' => $comercial
            ]);

        } catch (\Exception $e) {
            Log::error('Error al guardar imagen de producto: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Eliminar una imagen de producto
     */
    private function eliminarImagenProducto($imagenId, $productoId)
    {
        $imagen = SaprodImagen::where('id', $imagenId)
            ->where('producto_id', $productoId)
            ->first();

        if ($imagen) {
            $rutaCompleta = public_path($imagen->ruta_imagen);
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
            $imagen->delete();
            return true;
        }

        return false;
    }

    /**
     * Obtener todas las imágenes de un producto
     */
    public function getProductoImagenes($id)
    {
        $producto = Saprod::findOrFail($id);
        $comercial = session('comercialid', 1);

        $imagenes = SaprodImagen::where('producto_id', $producto->id)
            ->where('comercial_id', $comercial)
            ->orderBy('es_principal', 'desc')
            ->orderBy('orden')
            ->get();

        $imagenes->transform(function($img) {
            $img->url = asset($img->ruta_imagen);
            return $img;
        });

        return response()->json([
            'success' => true,
            'imagenes' => $imagenes
        ]);
    }

    public function subirImagenPrincipal(Request $request, $id)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $producto = Saprod::findOrFail($id);
        $comercial = session('comercialid', 1);

        // Eliminar imagen principal anterior si existe
        $imagenAnterior = SaprodImagen::where('producto_id', $producto->id)
            ->where('es_principal', true)
            ->first();

        if ($imagenAnterior) {
            $this->eliminarImagenProducto($imagenAnterior->id, $producto->id);
        }

        // Guardar nueva imagen principal
        $nuevaImagen = $this->guardarImagenProducto($request->file('imagen'), $producto, true, 0);

        if ($nuevaImagen) {
            return response()->json([
                'success' => true,
                'message' => 'Imagen principal actualizada',
                'imagen' => [
                    'id' => $nuevaImagen->id,
                    'url' => asset($nuevaImagen->ruta_imagen),
                    'es_principal' => true
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al guardar la imagen'
        ], 500);
    }

    /**
     * Subir imágenes adicionales (AJAX)
     */
    public function subirImagenesAdicionales(Request $request, $id)
    {
        $request->validate([
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $producto = Saprod::findOrFail($id);

        $maxOrden = SaprodImagen::where('producto_id', $producto->id)
            ->where('es_principal', false)
            ->max('orden') ?? 0;

        $imagenesGuardadas = [];
        $orden = $maxOrden;

        foreach ($request->file('imagenes') as $index => $archivo) {
            $orden++;
            $imagen = $this->guardarImagenProducto($archivo, $producto, false, $orden);

            if ($imagen) {
                $imagenesGuardadas[] = [
                    'id' => $imagen->id,
                    'url' => asset($imagen->ruta_imagen),
                    'orden' => $imagen->orden
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($imagenesGuardadas) . ' imagen(es) guardada(s)',
            'imagenes' => $imagenesGuardadas
        ]);
    }

    /**
     * Eliminar imagen (AJAX)
     */
    public function eliminarImagen(Request $request, $id, $imagenId)
    {
        $producto = Saprod::findOrFail($id);
        $comercial = session('comercialid', 1);

        $imagen = SaprodImagen::where('id', $imagenId)
            ->where('producto_id', $producto->id)
            ->where('comercial_id', $comercial)
            ->first();

        if (!$imagen) {
            return response()->json([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ], 404);
        }

        $rutaCompleta = public_path($imagen->ruta_imagen);
        if (file_exists($rutaCompleta)) {
            unlink($rutaCompleta);
        }

        $imagen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada correctamente'
        ]);
    }

    /**
     * Reordenar imágenes (AJAX)
     */
    public function reordenarImagenes(Request $request, $id)
    {
        $request->validate([
            'ordenes' => 'required|array',
            'ordenes.*.id' => 'required|integer',
            'ordenes.*.orden' => 'required|integer'
        ]);

        $producto = Saprod::findOrFail($id);
        $comercial = session('comercialid', 1);

        foreach ($request->ordenes as $item) {
            SaprodImagen::where('id', $item['id'])
                ->where('producto_id', $producto->id)
                ->where('comercial_id', $comercial)
                ->update(['orden' => $item['orden']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden actualizado'
        ]);
    }

    public function buscarproductoget($codprod, $comercial){

        $producto   = Saprod::where(['codprod'=>$codprod, "comercial" => $comercial])->first();
        session(['comercialid' => $comercial]);
        if(isset($producto) and isset($producto->id)){
            $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
                ->with(['padre'])
                ->where('comercial',$comercial)
                ->orderBy('codalte','asc')->get();

            $id = $producto->id;
            return view('product-edit', compact('instancias','producto', 'id'));
        }else{
            return response()->redirectTo('index');
        }

    }

    public function saprodexport($codalte)
    {
        $file = Excel::download(new SaprodExport($codalte), 'productos.xlsx');

        return $file;
    }

    public function inventarios(Request $request){
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $sqlcostoinv = "SELECT sum((a.preciod + a.preciod2)*b.existen) as suma, c.descrip
								from   saprod a , saexis b, sasucursal c
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and a.comercial = $comercialid
                                and c.fk_comercial = $comercialid
                            group by  c.descrip order by c.descrip
								";

        $costoinven = DB::select($sqlcostoinv);

        return view('reporteInventarios', compact('costoinven') );
    }

    public function updateSaprodData(Request $request)
    {
        $request->validate([
            'import_file' => [
                'required',
                'file'
            ],
        ]);

        Excel::import(new SaprodUpdate(), $request->file('import_file'));

        return redirect()->back()->with('status', 'Archivo Procesado Exitosamente');
    }

    public function index(Request $request)
    {

        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $sucursales = Sasucursal::where("fk_comercial", $comercialid)->get();

        $fechasaux      = '';
        $operacionesrep = '';

        $fechasreport   = (isset($request->fechasreport))? $request->fechasreport : '';
        $codprod        = (isset($request->codprod))? $request->codprod : '';
        $fechashoy      =  Carbon::now()->format('d/m/Y');
        $nofilterdate = 0;

        if(!$fechasreport) {
            $nofilterdate = 1;
            $fechasreport = $fechashoy;
        }

        $fechasaux = str_replace(' ','',$fechasreport);
        $fec1 = $fec2 = '';

        if(strpos($fechasaux,"to"))
            list($fec1, $fec2) = explode("to",$fechasaux);
        else {
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

        list($d1,$m1,$y1) = explode("/",$fec1);
        list($d2,$m2,$y2) = explode("/",$fec2);

        $fec1 = "$y1-$m1-$d1";
        $fec2 = "$y2-$m2-$d2";

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->with(['padre','hijos',  'productos'])
            ->where('comercial',$comercialid)
            ->orderBy('codalte','asc')
            ->get();

        // OBTENER ÚLTIMOS PRODUCTOS CREADOS
        $ultimosProductos = Saprod::where('comercial', $comercialid)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calcular total de productos activos
        $totalProductos = Saprod::where('comercial', $comercialid)
            ->where('activo', 1)
            ->count();


        if(isset($codprod) and $codprod !=''){

            $compras = DB::table('saitemcom')
                ->select([
                    'id',
                    'tipocom as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    'costod as precio',
                    'codubic as dep1',
                    DB::raw("'' as dep2"),
                    'descrip1 as descripcion',
                    DB::raw("'COMPRA' as tipo_movimiento")
                ])
                ->where('coditem', $codprod)
                ->whereBetween('fechae', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $ventas = DB::table('saitemfac')
                ->select([
                    'id',
                    'TipoFac as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    'costod as precio',
                    'codubic as dep1',
                    DB::raw("'' as dep2"),
                    'Descrip1 as descripcion',
                    DB::raw("'VENTA' as tipo_movimiento")
                ])
                ->where('CodItem', $codprod)
                ->whereBetween('FechaE', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $operaciones = DB::table('saitemopi')
                ->select([
                    'id',
                    'tipoopi as tipo',
                    'numerod',
                    'FechaE',
                    DB::raw("date_format(fechae,'%d/%m/%Y') as fecha"),
                    DB::raw('(cantidad*signo) as cantidad'),
                    'fk_sucursal',
                    'preciod as costo',
                    DB::raw('0 as precio'),
                    'codubic as dep1',
                    'codubic2 as dep2',
                    'Descrip1 as descripcion',
                    DB::raw("'OPERACION_INTERNA' as tipo_movimiento")
                ])
                ->where('CodItem', $codprod)
                ->whereBetween('FechaE', ["$fec1 00:00:00", "$fec2  23:55:00"]);

            $operacionesrep = $compras->union($ventas)->union($operaciones)
                ->orderBy('FechaE', 'asc')
                ->get();
        }

        return view('product-list', compact(
            'instancias',
            'sucursales',
            'codprod',
            'operacionesrep',
            'fechasreport',
            'ultimosProductos',
            'totalProductos',
        ));
    }

    public function existencias()
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }
        $instancias = '';

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
                               ->where('comercial',$comercial)
                               ->orderBy('descrip','asc')
                               ->get();

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->get();

        return view('existenciasInstancias', compact( 'sucursales', 'instancias', 'comercial') );
    }

    public function existenciasLubricantes()
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte")
            ->whereRaw("nivel=2 AND   tipoins=0 and codalte like '1.%'")
            ->orderBy('descrip','asc')->get();

        $instanciarr = $instancias->pluck('codinst');
        $instaccodin = implode(",", $instanciarr->toArray());

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->get();
        $sucursalarr = $sucursales->pluck('id');
        $sucursalIds = implode(",", $sucursalarr->toArray());

        $sucursales  = Sasucursal::where("fk_comercial", $comercial)->get();

        $query = DB::table('saprod as productos')
            ->join('sainsta as instancias', 'productos.codinst', '=', 'instancias.codinst')
            ->join('saexis as existencias', 'productos.codprod', '=', 'existencias.codprod')
            ->join('sasucursal as sucursales', 'existencias.fk_sucursal', '=', 'sucursales.id')
            ->select(
                'instancias.codinst',
                'existencias.fk_sucursal',
                DB::raw('SUM(existencias.existen) as total_cantidad')
            )
            ->where('productos.comercial', $comercial)
            ->whereRaw("existencias.fk_sucursal in ($sucursalIds) and productos.codinst in ($instaccodin)")
            ->where('existencias.existen', '>', 0)
            ->groupBy('existencias.fk_sucursal', 'instancias.codinst')
            ->having('total_cantidad', '>', 0)
            ->orderBy('total_cantidad','desc')
            ->orderBy('instancias.codinst')
            ->orderBy('existencias.fk_sucursal')
            ->get();

        //$query = DB::select($consulta);

        $vectorsucursales = [];
        foreach ($sucursales as $sucursal){
            if(!isset($vectorsucursales[$sucursal->id])){
                $vectorsucursales[$sucursal->id] = $sucursal->descrip;
            }
        }

        $vectorinstancias = [];
        foreach ($instancias as $instancia){
            if(!isset($vectorinstancias[$instancia->codinst])){
                $vectorinstancias[$instancia->codinst] = $instancia->descrip;
            }
        }

        $arraysucursal = array();
        $arrayinstanci = array();
        $arraycantidad = array();


        foreach ($query as $item) {
            if(!isset($arraysucursal[$item->fk_sucursal]))
                $arraysucursal[$item->fk_sucursal] = $vectorsucursales[$item->fk_sucursal];

            if(!isset($arrayinstanci[$item->codinst]) and isset($vectorinstancias[$item->codinst]))
                $arrayinstanci[$item->codinst] = $vectorinstancias[$item->codinst];

            if(!isset($arraycantidad[$item->codinst][$item->fk_sucursal]))
                $arraycantidad[$item->codinst][$item->fk_sucursal] = 0;

            $arraycantidad[$item->codinst][$item->fk_sucursal] += $item->total_cantidad;
        }

       // asort($arrayinstanci);

        return view('existenciasLubricantes', compact( 'arraysucursal', 'arrayinstanci', 'arraycantidad') );
    }

    public function existenciasphp(Request $request)
    {
        $codinst    = $request->codinst;
        $fksucursal = $request->fksucursal;


        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $instancias = Sainsta::selectRaw("  Descrip as label, descrip, id, nivel, codinst , codalte, insPadre")
                               ->where('comercial',$comercial)
                               ->orderBy('descrip','asc')
                               ->get();
        $insPadre = 0;

        $sucursales = Sasucursal::where("fk_comercial", $comercial)->get();

        $instanciaselected = '';
        foreach ($instancias as $instancia){
            if($instancia->codinst == $codinst){
                $instanciaselected = $instancia;
                $insPadre = $instancia->insPadre;
                break;
            }
        }
        return view('existenciasInstanciasphp', compact('fksucursal', 'insPadre', 'codinst', 'sucursales', 'instancias', 'instanciaselected', 'comercial') )->render();
    }

    public function json()
    {
        $comercial  = session('comercialid') ;
        $all = Saprod::where('comercial',$comercial)->with(['instancia'])->orderBy('descrip','asc')->get();
        $aux = [];
        $productos = [];
        $noimage = URL::asset('build/images/noimagen.jpg');
        foreach ($all as $item){
            $aux = [
                "id"            => "$item->id",
                "price"         => "$item->costod3",
                "exdecimal"     => "$item->exdecimal",
                "image"         => (isset($item->productImg))? '': $noimage,
                "productTitle"  => "$item->descrip",
                "category"      => $item->instancia->descrip
            ];

            array_push($productos,$aux);
        }
        return response()->json($productos );
    }

    public function productossucursales(Request $request)
    {
        $comercialid = session('comercialid');

        if (!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $instancias = Sainsta::porComercial($comercialid)
            ->whereIn('nivel', [1 ]) // Niveles 1 y 2
            ->orderBy('descrip', 'asc')
            ->get();

        $allsucursales = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')->get();

        $existenciaact = (isset($request->existenciaact ))? $request->existenciaact : '';
        $fksucursal    = (isset($request->fksucursal    ))? $request->fksucursal    : '';
        $codinst       = (isset($request->codinst       ))? $request->codinst       : '';
        $fechasreport  = $request->fechasreport;
        $fechasreport2 = (isset($request->fechasreport2))? $request->fechasreport2 :'';

        $fechasaux     = str_replace(' ', '', $fechasreport);
        $fechasaux2    = str_replace(' ', '', $fechasreport2);
        $fec1  = $fec2  = $fecha1 = $fecha2 = '';
        $d22   = $m22 = $y22 = $d12   = $m12 =$y12 = $fec12 = $fec22 = '';
        $listadoMesAnterior = collect();

        $itemventas    = [];
        $sucursales    = [];
        $sucursales2   = [];
        $itemventas2   = [];
        $cantidadprod  = [];
        $cantidadprod2 = [];
        $preciodprod   = [];
        $costodprod    = [];

        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if($fechasreport !=''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 != ''){

            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);

            $fecha1 = $fec1;
            $fecha2 = $fec2;

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";

            $listado = $this->obtenerVentasPeriodo($comercialid, $fec1, $fec2, $fksucursal,$codinst);

            $fec12 = $fec22 = '';
            if (strpos($fechasaux2, "to")) {
                list($fec12, $fec22) = explode("to", $fechasaux2);
            } else {
                if($fechasreport2 != ''){
                    list($d12, $m12, $y12) = explode("/", $fechasreport2);
                    $fec12 = "$d12/$m12/$y12";
                    $fechasreport2= "$fec12 to $fec12";
                }
            }

            if (strpos($fec12, "/")) {
                list($d12, $m12, $y12) = explode("/", $fec12);
                if(!$fec22)
                    $fec22= $fec12;
                list($d22, $m22, $y22) = explode("/", $fec22);
                $fec12 = "$y12-$m12-$d12";
                $fec22 = "$y22-$m22-$d22";
            }

        }

        if(  $fec1 != '' and $codinst){

            if ($fec22 != '') {
                $listadoMesAnterior = $this->obtenerVentasPeriodo($comercialid, $fec12, $fec22, $fksucursal, $codinst);
                list($sucursales2, $cantidadprod2, $itemventas2, $costodprod ) = $this->procesarDatosVentas($listadoMesAnterior, 0);
            }

            list($sucursales, $cantidadprod, $itemventas, $costodprod) = $this->procesarDatosVentas($listado, 1);

            asort($sucursales);

            if(!isset($sucursales)) $sucursales = [];

            if(!isset($cantidadprod))  $cantidadprod = [];
            if(!isset($cantidadprod2))  $cantidadprod2 = [];

            if(isset($cantidadprod) and count($cantidadprod) > 0)
                foreach($cantidadprod as $index => $val){
                    if(!isset($cantidadprod2[$index]))
                        $cantidadprod2[$index] = $val;
                }

            if(isset($sucursales2) and count($sucursales2) > 0){
                foreach($sucursales2 as $index => $val){
                    if(!isset($sucursales[$index])){
                        $sucursales[$index] = $val;
                    }
                }
            }

            if(isset($cantidadprod2) and count($cantidadprod2) > 0)
                foreach($cantidadprod2 as $index => $val){
                    if(!isset($cantidadprod[$index])){
                        $cantidadprod[$index] = $val;
                    }
                }

            foreach($itemventas2 as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas[$index][$index2])){
                        $itemventas[$index][$index2] = $arr;
                    }
                }
            }

            foreach($itemventas as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas2[$index][$index2])){
                        $itemventas2[$index][$index2] = $arr;
                    }
                }
            }

        }

        return view('productosSucursales', compact(
            'fecha1',
            'fecha2',
            'instancias',
            'fechasreport',
            'fechasreport2',
            'sucursales',
            'itemventas',
            'itemventas2',
            'codinst',
            'cantidadprod',
            'cantidadprod2',
            'fksucursal',
            'allsucursales',
            'existenciaact'
        ));
    }
    public function resultadosucursales(Request $request)
    {
        $comercialid = session('comercialid');

        if (!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $instancias = Sainsta::porComercial($comercialid)
            ->whereIn('nivel', [1 ]) // Niveles 1 y 2
            ->where('tipoins',0)
            ->orderBy('descrip', 'asc')
            ->get();

        $allsucursales  = Sasucursal::where('fk_comercial', $comercialid)->orderBy('descrip','asc')->get();
        $existenciaact  = (isset($request->existenciaact ))? $request->existenciaact : '';
        $fksucursal     = (isset($request->fksucursal    ))? $request->fksucursal    : '';
        $codinst        = (isset($request->codinst       ))? $request->codinst       : '';
        $fechasreport   = $request->fechasreport;
        $fechasreport2  = (isset($request->fechasreport2))? $request->fechasreport2 :'';

        $fechasaux      = str_replace(' ', '', $fechasreport);
        $fechasaux2     = str_replace(' ', '', $fechasreport2);
        $fec1  = $fec2  = $fecha1 = $fecha2 = '';
        $d22   = $m22 = $y22 = $d12   = $m12 =$y12 = $fec12 = $fec22 = '';
        $listadoMesAnterior = collect();

        $itemventas    = [];
        $sucursales    = [];
        $sucursales2   = [];
        $itemventas2   = [];
        $cantidadprod  = [];
        $cantidadprod2 = [];
        $preciodprod   = [];
        $costodprod    = [];
        if (strpos($fechasaux, "to")) {
            list($fec1, $fec2) = explode("to", $fechasaux);
        } else {
            if($fechasreport !=''){
                list($d1, $m1, $y1) = explode("/", $fechasreport);
                $fec1 = "$d1/$m1/$y1";
                $fec2 = $fec1;
                $fechasreport = "$fec1 to $fec2";
            }
        }

        if($fec1 != ''){

            list($d1, $m1, $y1) = explode("/", $fec1);
            list($d2, $m2, $y2) = explode("/", $fec2);

            $fecha1 = $fec1;
            $fecha2 = $fec2;

            $fec1 = "$y1-$m1-$d1";
            $fec2 = "$y2-$m2-$d2";


            $listado = $this->obtenerVentasPeriodo($comercialid, $fec1, $fec2, $fksucursal, $codinst);

            $fec12 = $fec22 = '';
            if (strpos($fechasaux2, "to")) {
                list($fec12, $fec22) = explode("to", $fechasaux2);
            } else {
                if($fechasreport2 != ''){
                    list($d12, $m12, $y12) = explode("/", $fechasreport2);
                    $fec12 = "$d12/$m12/$y12";
                    $fechasreport2= "$fec12 to $fec12";
                }
            }

            if (strpos($fec12, "/")) {
                list($d12, $m12, $y12) = explode("/", $fec12);
                if(!$fec22)
                    $fec22= $fec12;
                list($d22, $m22, $y22) = explode("/", $fec22);
                $fec12 = "$y12-$m12-$d12";
                $fec22 = "$y22-$m22-$d22";
            }

        }

        if($fksucursal != '' and $fec1 != '' and $codinst){

            if ($fec22 != '') {
                $listadoMesAnterior = $this->obtenerVentasPeriodo($comercialid, $fec12, $fec22, $fksucursal, $codinst);
                list($sucursales2, $cantidadprod2, $itemventas2, $costodprod, $preciodprod) = $this->procesarDatosVentas($listadoMesAnterior, 0);
            }

            list($sucursales, $cantidadprod, $itemventas, $costodprod, $preciodprod) = $this->procesarDatosVentas($listado, 1);

            asort($sucursales);

            if(!isset($sucursales)) $sucursales = [];

            if(!isset($cantidadprod))  $cantidadprod = [];
            if(!isset($cantidadprod2))  $cantidadprod2 = [];

            if(isset($cantidadprod) and count($cantidadprod) > 0)
                foreach($cantidadprod as $index => $val){
                    if(!isset($cantidadprod2[$index]))
                        $cantidadprod2[$index] = $val;
                }

            if(isset($sucursales2) and count($sucursales2) > 0){
                foreach($sucursales2 as $index => $val){
                    if(!isset($sucursales[$index])){
                        $sucursales[$index] = $val;
                    }
                }
            }

            if(isset($cantidadprod2) and count($cantidadprod2) > 0)
                foreach($cantidadprod2 as $index => $val){
                    if(!isset($cantidadprod[$index])){
                        $cantidadprod[$index] = $val;
                    }
                }

            foreach($itemventas2 as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas[$index][$index2])){
                        $itemventas[$index][$index2] = $arr;
                    }
                }
            }

            foreach($itemventas as $index => $items){
                foreach($items as $index2 => $arr){
                    if(!isset($itemventas2[$index][$index2])){
                        $itemventas2[$index][$index2] = $arr;
                    }
                }
            }

        }

        return view('resultadosucursales', compact(
            'fecha1',
            'fecha2',
            'codinst',
            'fechasreport',
            'fechasreport2',
            'sucursales',
            'itemventas',
            'itemventas2',
            'cantidadprod',
            'costodprod',
            'preciodprod',
            'cantidadprod2',
            'fksucursal',
            'allsucursales',
            'instancias',
            'existenciaact'
        ));
    }
    private function obtenerVentasPeriodo($comercialid, $fechaInicio, $fechaFin, $fksucursal, $codinst)
    {
        $datainst = '';
        $codalte  = '';
        if(isset($codinst) and $codinst >0){
            $instancia = Sainsta::where('codinst', $codinst)->first();
            $codalte   = (isset($instancia->codalte))? $instancia->codalte : '';
        }

        $datainst = "descomp = 0 ";
        if($codalte !='')
            $datainst .= " and codalte like '$codalte%'";

        $datos = Saitemfac::whereRaw("TipoFac in ('A','B')")
            ->selectRaw("fk_sucursal, CodItem, SUM(Cantidad*Signo) as salidas, SUM(Cantidad*costodoriginal*Signo) as costod, SUM(Cantidad*preciod*Signo) as preciod")
            ->with(['sucursal', 'producto.instancia' => function($q) use($datainst) {
                if($datainst != ''){
                    $q->whereRaw($datainst);
                }
                $q = $q->orderBy('codalte', 'asc');
            }])
            ->whereRaw("esserv = 0 and coditem<>'0101'")
            ->whereHas('sucursal.comercial', function($q) use ($comercialid) {
                $q->where('fk_comercial', $comercialid);
            })
            ->whereBetween('FechaE', [$fechaInicio . ' 00:00:00.00', $fechaFin . ' 23:58:22.00'])
            ->groupBy(['fk_sucursal', 'CodItem'])->orderBy('fk_sucursal');

        if($comercialid == 1 and $codalte != ''){
            $datos = $datos->whereRaw("CodItem in  (select y.codprod from saprod y, sainsta z where  z.codinst = y.codinst and z.codalte like '$codalte%')");
        }

        if(isset($fksucursal) and $fksucursal != '' and $fksucursal > 0){
            $datos =  $datos->where('fk_sucursal', $fksucursal);
        }


        $datos =  $datos->get();

        return $datos;

    }
    private function procesarDatosVentas($listado, $agruparsucu): array
    {
        $sucursales   = [];
        $itemventas   = [];
        $cantidadprod = [];
        $costodprod   = [];
        $preciodprod  = [];
        $productos    = [];

        if($agruparsucu == 1){

            if (isset($listado)) {
                foreach ($listado as $prodsuc) {
                    if (!isset($sucursales[$prodsuc->sucursal->id]))
                        $sucursales[$prodsuc->sucursal->id] = $prodsuc->sucursal->descrip;

                    if (!isset($cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;

                    if (!isset($costodprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $costodprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;

                    if (!isset($preciodprod[$prodsuc->CodItem . $prodsuc->sucursal->id]))
                        $preciodprod[$prodsuc->CodItem . $prodsuc->sucursal->id] = 0;

                    if (!isset($productos[$prodsuc->CodItem]))
                        $productos[$prodsuc->CodItem] = ['codprod'=>$prodsuc->producto->codprod, 'existen'=>$prodsuc->producto->existen];

                    if(!isset($prodsuc->producto->instancia))
                        dd($prodsuc);

                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['descrip']   = $prodsuc->producto->descrip;
                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['exdecimal'] = $prodsuc->producto->exdecimal;
                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['existen'] = $prodsuc->producto->existen;

                    $cantidadprod[$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->salidas;
                    $costodprod  [$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->costod;
                    $preciodprod [$prodsuc->CodItem . $prodsuc->sucursal->id] += $prodsuc->preciod;
                }
            }

            return [$sucursales, $cantidadprod, $itemventas, $costodprod, $preciodprod];
        }else{


            if (isset($listado)) {
                foreach ($listado as $prodsuc) {

                    if (!isset($sucursales[$prodsuc->sucursal->id]))
                        $sucursales[$prodsuc->sucursal->id] = $prodsuc->sucursal->descrip;

                    if (!isset($cantidadprod[$prodsuc->CodItem ]))
                        $cantidadprod[$prodsuc->CodItem ] = 0;

                    if (!isset($costodprod[$prodsuc->CodItem]))
                        $costodprod[$prodsuc->CodItem ] = 0;

                    if (!isset($preciodprod[$prodsuc->CodItem]))
                        $preciodprod[$prodsuc->CodItem ] = 0;

                    if(!isset($prodsuc->producto->instancia))
                        dd($prodsuc);

                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['descrip']   = $prodsuc->producto->descrip;
                    $itemventas[$prodsuc->producto->instancia->descrip][$prodsuc->CodItem]['exdecimal'] = $prodsuc->producto->exdecimal;

                    $cantidadprod[$prodsuc->CodItem] += $prodsuc->salidas;
                    $costodprod  [$prodsuc->CodItem] += $prodsuc->costod;
                    $preciodprod [$prodsuc->CodItem] += $prodsuc->preciod;
                }
            }

            return [ $sucursales, $cantidadprod, $itemventas, $costodprod,$preciodprod];

        }
    }

    public function busquedaHomeProd(Request $request)
    {
        $busqueda = $request->busqueda;
        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(codprod like '%$value%' or descrip like '%$value%' or refere like '%$value%' or marca like '%$value%' or descrip2 like '%$value%')";
                $numerito++;
            }
        }

        $comercial = session('comercialid');

        // Obtener los productos
        $productos = Saprod::where('comercial', $comercial)
            ->whereRaw($cadena)
            ->orderBy('updated_at', 'desc')
            ->limit(60)
            ->get();

        // Obtener las sucursales del comercial
        $sucursales = Sasucursal::where('fk_comercial', $comercial)
            ->orderBy('descrip')
            ->get();

        // Para cada producto, obtener las existencias por sucursal
        foreach ($productos as $producto) {
            if(!isset($producto->existencias_por_sucursal))
                $producto->existencias_por_sucursal = [];

            $existencias = Saexis::where('codprod', $producto->codprod)
                ->whereIn('fk_sucursal', $sucursales->pluck('id'))
                ->where('existen','<>',0)
                ->with('deposito')
                ->get();

            $producto->existencias_por_sucursal = $existencias;
        }


        return view('layouts.ajaxbusqueda', compact('productos', 'sucursales'))->render();
    }

    public function saprodsucursal(Request $request)
    {
        $sucursalid = str_replace("300", "", $request->sucursal);
        $productos = $request->productos;
        $productos = json_decode($productos);

        if (isset($productos))
            foreach ($productos as $producto){
                $aux = Saprodsucursal::where(['codprod' => $producto->codprod, 'fk_sucursal'=>$sucursalid])->first();
                if(!$aux){
                    $rel              = new Saprodsucursal();
                    $rel->codprod     = $producto->codprod;
                    $rel->fk_sucursal = $sucursalid;
                    $rel->save();
                }
            }

        return response()->json(['success'=>'success']);
    }

    public function list(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;

        $productos = Saprod::where('comercial',$comercial)
            ->whereRaw("codprod not in (select codprod from saprodsucursal where fk_sucursal=$sucursalid )")->get()->take(300);

        $servicios = Saserv::where('comercial',$comercial)
            ->whereRaw("codserv not in (select codserv from saservsucursal where fk_sucursal=$sucursalid )")->limit(300)->get();

        return response()->json(['success'=>'success', 'newproductos' => $productos, 'newservicios' => $servicios]);
    }

    public function productosinstsancias(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codinst     = $request->codinst;

        $sqlcostoinv = "SELECT  a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic = e.codubic
                                and c.fk_comercial = $comercialid
								and d.codinst = a.codinst
                                and a.codinst = $codinst
								and e.comercial = $comercialid
								and b.existen > 0
                        order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado]);

    }

    public function productosinstsanciascodalte(Request $request)
    {
        $sucursalid  = str_replace("300","",$request->sucursal);
        $sucursal    = Sasucursal::find($sucursalid);
        $comercialid = $sucursal->fk_comercial;
        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from   saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where  a.codprod = b.codprod
                                and b.fk_sucursal = c.id
								and b.codubic   = e.codubic
                                and c.fk_comercial = $comercialid
								and a.comercial = $comercialid
								and d.comercial = $comercialid
								and e.comercial = $comercialid
								and d.codinst   = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen > 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        return response()->json(['success'=>'success', 'listado' => $listado, 'sqlcostoinv' => $sqlcostoinv]);

    }

    public function viewprodinstsanciascodalte(Request $request)
    {
        $comercial  = session('comercialid') ;
        if(!$comercial) {
            session(['comercialid' => 1]);
            $comercial = 1;
        }

        $codalte     = $request->codalte;
        $len         = strlen($codalte);

        $sqlcostoinv = "SELECT a.preciod, a.descrip, a.codprod, e.codubic, b.existen, e.descrip as deposito
								from saprod a , saexis b, sasucursal c, sainsta d, sadepo e
								where a.codprod    = b.codprod
                                and b.fk_sucursal  = c.id
								and b.codubic      = e.codubic
                                and c.fk_comercial = $comercial
								and a.comercial    = $comercial
								and d.comercial    = $comercial
								and e.comercial    = $comercial
								and d.codinst      = a.codinst
                                and left(d.codalte,$len) = '$codalte'
								and b.existen <> 0
                                order by a.descrip
								";

        $listado = DB::select($sqlcostoinv);

        $productos    = [];
        $deposito     = [];
        $existencias  = [];

        foreach($listado as $producto){

            if(!isset($productos[$producto->codprod]))
                $productos[$producto->codprod] = [];

            $productos[$producto->codprod]['descrip']    = $producto->descrip;
            $productos[$producto->codprod]['preciod']    = $producto->preciod;

            if(!isset($deposito[$producto->codubic]))
                $deposito[$producto->codubic] = $producto->deposito;

            if(!isset($existencias[$producto->codprod][$producto->codubic]))
                $existencias[$producto->codprod][$producto->codubic] = 0;

            $existencias[$producto->codprod][$producto->codubic] = $producto->existen;
        }

        return view('productosallinstsancias', compact('productos', 'deposito', 'existencias') )->render();


    }

    public function listprodubic(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function listprodubicinv(Request $request)
    {
        $sucursalid = str_replace("300","",$request->sucursal);
        $sucursal   = Sasucursal::find($sucursalid);
        $comercial  = $sucursal->fk_comercial;
        $codprod    = $request->codprod;

        $allsucursa = Sasucursal::where('fk_comercial',$comercial)->get();
        $auxsucu    = [];

        foreach ($allsucursa as $sucu){
            array_push( $auxsucu, $sucu->id);
        }
        $auxsucu = implode(',' , $auxsucu);

        $existencias = Saexis::whereRaw("fk_sucursal in ($auxsucu) and codprod='$codprod' and existen > 0")
            ->orderBy('codubic')->get();

        return response()->json(['success'=>'success', 'existencias' => $existencias]);
    }

    public function create()
    {
        $comercial  = session('comercialid') ;
        $instancias = Sainsta::selectRaw("concat( repeat('&nbsp;',((nivel-1)*4)), Descrip ) as label, descrip, id, nivel, codinst ")
            ->with(['padre'])
            ->where('comercial',$comercial)
            ->orderBy('codalte','asc')->get();

        $last   = '';
        $product = Saprod::orderBy('id','desc')->first();
        if(isset($product) and $product->codprod != '')
            $last = $product->codprod;

        return view('product-create', compact('instancias','last') );
    }

    public function checkcodprod($codprod)
    {
        $check   = 0;
        $comercial = session('comercialid') ;

        if($codprod != '')
            $product = Saprod::where(['codprod' => $codprod, 'comercial' => $comercial])->first();
            if(isset($product) and $product->codprod != '')
                $check = 0;
            else
                $check = 1;

        return response()->json(['check' => $check ]);
    }

    public function store(Request $request)
    {
        $comercial = session('comercialid') ;

        $newprod = new Saprod();
        $newprod->fill($request->all());
        $newprod->comercial = $comercial;
        $newprod->save();
        return redirect()->route('productos.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $producto   = Saprod::find($id);
        $comercial = session('comercialid') ;

        $instancias = Sainsta::selectRaw("codinst, descrip, nivel, codalte")
            ->where('comercial', $comercial)
            ->orderBy('codalte', 'asc')
            ->get();

        // Procesar el label con indentación para el select
        $instanciasProcesadas = $instancias->map(function($item) {
            $item->label = str_repeat('&nbsp;', ($item->nivel - 1) * 4) . e($item->descrip);
            return $item;
        });

        return view('product-edit', compact('instanciasProcesadas','producto', 'id'));
    }

    public function update(Request $request, $id)
    {
        $comercial = session('comercialid') ;

        $producto  = Saprod::find($id);
        $producto->fill($request->all());
        $producto->esexento = 1;  //// luego ver como manejamos esto

        if(isset($request->preciod)) {
            $preciod = $request->preciod;
            $coma = substr_count($preciod, ',');
            $punto = substr_count($preciod, '.');

            if ($coma > 0 and $punto > 0) {
                $preciod = str_replace(".", '', $preciod);
                $preciod = str_replace(",", '.', $preciod);
            }
            if ($coma > 0 and !$punto)
                $preciod = str_replace(",", '.', $preciod);
            $producto->preciod = $preciod;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod)){
            $costod =  $request->costod;

            $coma  = strpos($costod, ',');
            $punto = strpos($costod, '.');

            if($coma>0 and $punto>0){
                $costod = str_replace(".",'',$costod);
                $costod = str_replace(",",'.',$costod);
            }
            if($coma>0  and !$punto)
                $costod = str_replace(",",'.',$costod);

            $producto->costod = $costod;
        }
        ////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod2)){
            $costod2 =  $request->costod2;
            $coma  = substr_count($costod2, ',');
            $punto = substr_count($costod2, '.');

            if($coma>0 and $punto>0){
                $costod2 = str_replace(".",'',$costod2);
                $costod2 = str_replace(",",'.',$costod2);
            }
            if($coma>0  and !$punto)
                $costod3 = str_replace(",",'.',$costod2);
            $producto->costod2 = $costod2;
        }
        //////////////////////////////////////////////////////////////////////////////
        if(isset($request->costod3)) {
            $costod3 = $request->costod3;
            $coma = substr_count($costod3, ',');
            $punto = substr_count($costod3, '.');

            if ($coma > 0 and $punto > 0) {
                $costod3 = str_replace(".", '', $costod3);
                $costod3 = str_replace(",", '.', $costod3);
            }
            if ($coma > 0 and !$punto)
                $costod3 = str_replace(",", '.', $costod3);
            $producto->costod3 = $costod3;
        }
        ////////////////////////////////////////////////////////////////////////////

        if(!$request->exdecimal)
            $producto->exdecimal = 0;

        if(!$request->activo)
            $producto->activo = 0;

        $producto->save();

        $prodsucursal = Saprodsucursal::with('producto')->where('codprod', $producto->codprod)->get();

        if($prodsucursal)
            foreach ($prodsucursal as $item){
                if($item->producto->comercial == $comercial)
                    $item->delete();
            }

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente',
            'producto' => $producto
        ]);
    }

    public function establecerImagenPrincipal(Request $request, $id, $imagenId)
    {
        $producto = Saprod::findOrFail($id);
        $comercial = session('comercialid', 1);

        // Verificar que la imagen pertenece al producto
        $imagen = SaprodImagen::where('id', $imagenId)
            ->where('producto_id', $producto->id)
            ->where('comercial_id', $comercial)
            ->first();

        if (!$imagen) {
            return response()->json([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ], 404);
        }

        // Quitar principal de todas las imágenes del producto
        SaprodImagen::where('producto_id', $producto->id)
            ->where('comercial_id', $comercial)
            ->update(['es_principal' => false]);

        // Establecer esta imagen como principal
        $imagen->es_principal = true;
        $imagen->save();

        return response()->json([
            'success' => true,
            'message' => 'Imagen principal actualizada'
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
