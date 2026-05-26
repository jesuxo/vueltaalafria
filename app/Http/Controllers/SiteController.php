<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Promocion;
use App\Models\Sainsta;
use App\Models\Saprod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;

class SiteController extends Controller
{
    public $promociones;

    public function __construct()
    {
        $this->middleware('auth')->except(['webhooks','index','panelclientes', 'promo', 'promoid', 'pago', 'procesar', 'encoded', 'tokencsrf', 'promociones', 'gourl', 'gourlpromo']);
    }

    public function webhooks(){


        $sid = "AC70691ae2f04b13269e8c65aa3a39df66";
        $token = "[AuthToken]";
        $twilio = new Client($sid, $token);

        $message = $twilio->messages
            ->create("whatsapp:+584247329670", // to
                array(
                    "from" => "whatsapp:+14155238886",
                    "contentSid" => "HXb5b62575e6e4ff6129ad7c8efe1f983e",
                    "contentVariables" => "{}",
                    "body" => "Your Message testing"
                )
            );


        print($message->sid);

        $array = [
            'message' => 'good'
        ];
        return response()->json(json_encode($array));
    }

    public function panelclientes(){
        $home = 1;
        return view('auth.login',compact('home'));
    }

    public function promociones(Request $request, $busqueda = null){
        $paginapromo = 1;
        $promociones = Promocion::where(['activo' => 1, 'pendiente' => 0]);

        if($busqueda) {
            $busqueda = str_replace("'",'', $busqueda);
            $sql = '';
            $vector = explode(' ', $busqueda);
            foreach ($vector as $index => $item){

                $iter   = $item;
                $l_iter = strlen($iter);
                $ultima = substr($iter, -1, 1);

                if($ultima == 's' and $item != 'ups')
                    $iter = substr($iter, 0, $l_iter-1);

                if($index > 0) $sql .= " and ";
                $sql .= " ( descrip like '%$iter%'  or public_title like '%$iter%' or public_descrip like '%$iter%' ) ";
            }
            $promociones = $promociones->whereRaw("( $sql )");
        }

        $gosearchpromo = $busqueda;

        $promociones = $promociones->orderBy('descrip', 'desc')->get();
        return view('promociones', compact('promociones', 'paginapromo', 'gosearchpromo'));
    }

    public function agregados()
    {
        $count  = 0;

        if(auth()->id()) {
            $compra = Compra::with('items')->where(['encurso' => 1, 'fk_user' => auth()->id()])->first();

            if (isset($compra->items[0]))
                $count = count($compra->items);

            return response()->json(['count' => $count], 200);
        }
    }

    public function tokencsrf()
    {
        dd(csrf_token());
    }

    public function encoded(Request $request)
    {
        $msg = $request->msg;
        $msg = 'https://wa.me/584123268315?text='.urlencode($msg);

        return response()->json( $msg, 200);
    }

    public function pago($amount)
    {
        return view('payment', compact('amount'));
    }

    public function bienvenido()
    {
        $instPpales =  $instancias = Sainsta::where('destacada', 1)->orderBy('codalte')->get();
        return view('home.bienvenido', compact('instPpales'));
    }

    public function procesar(Request $request)
    {
        /* \Stripe\Stripe::setApiKey('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');

         //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
         //sk_test_51Hbp7MCKOhyGF8Q2ZfFKwYdN1fpwVZJOlB4BuXstW774ne98x9bG8g0TxBiIfqGnvbQLXrkRRsTjSuBM0xQ04BMh00KGlKJg3i

         $token  = $request->stripeToken;
         $nombre = $request->nombre;
         $cedula = $request->cedula;
         $monto  = $request->monto;

         if(!$token){
             $error = "Tarjeta de credito no verificada";
             return redirect()->route('pagar.monto', $monto)->withErrors($error);
         }
         $nombre = $cedula.' '.$nombre;
         $monto = $monto;
         $error = '';

         try {
             if(isset($nombre) and  $nombre){
                 $charge = \Stripe\Charge::create([
                     'amount'      => $monto,
                     'currency'    => 'usd',
                     'description' => $nombre,
                     'source'      => $token
                 ]);

                 return view('payment')->with(['success' => 'success']);
             }else{
                 $error .= "Verifique datos requridos";
             }

         } catch (\Stripe\Error\Card $e) {
             $error .= $e->getMessage();
         }
         catch(\Stripe\Exception\CardException $e) {

             $error .= 'Status is:'  . $e->getHttpStatus()     . ' ';
             $error .= 'Type is:'    . $e->getError()->type    . ' ';
             $error .= 'Code is:'    . $e->getError()->code    . ' ';
             $error .= 'Param is:'   . $e->getError()->param   . ' ';
             $error .= 'Message is:' . $e->getError()->message . ' ';

         } catch (\Stripe\Exception\RateLimitException $e) {
             $error .= 'Too many requests made to the API too quickly ';
         } catch (\Stripe\Exception\InvalidRequestException $e) {
             $error .= 'Invalid parameters were supplied to Stripe s API ';
         } catch (\Stripe\Exception\AuthenticationException $e) {
             $error .= 'Authentication with Stripe s API failed ';
             // (maybe you changed API keys recently)
         } catch (\Stripe\Error\ApiConnection $e) {
             $error .= 'Could not connect to Stripe ';
         } catch (\Stripe\Exception\ApiConnectionException $e) {
             $error .= 'Network communication with Stripe failed ';
         } catch (\Stripe\Exception\ApiErrorException $e) {
             $error .= 'Display a very generic error to the user, and maybe send ';
             // yourself an email
         } catch (Exception $e) {
             $error .= 'Something else happened, completely unrelated to Stripe ';
         }



         if($error){
             return redirect()->route('pagar.monto', $monto)->withErrors($error);
         }

         return view('payment')->with(['error' => $error]);*/
    }

    public function gourl(Request $request)
    {
        $busqueda = $request->busqueda;
        $busqueda = str_replace("'",'', $busqueda);
        return redirect()->route('url.busqueda', $busqueda);
    }

    public function gourlpromo(Request $request)
    {
        $busqueda = $request->busqueda;
        if($busqueda)
            return redirect()->route('url.busquedapromo', $busqueda);
        else
            return redirect()->route('promociones');

    }

    public function promo($id)
    {
        $data = Promocion::find($id);
        if(!isset($data))
            $data = '';

        $paginapromo = 1;

        return view('promocion', compact('data', 'paginapromo'));
    }

    public function promoid($id)
    {
        $data = Promocion::find($id);
        if(!isset($data))
            $data = '';

        $paginapromo = 1;

        return view('home.layouts.partials.promo', compact('data', 'paginapromo'))->render();
    }

    public function index(Request $request, $id = null)
    {
        //dd(bcrypt('Droarca001'));
        $comercialid = session('comercialid');
        if(!$comercialid) {
            session(['comercialid' => 1]);
            $comercialid = 1;
        }

        $routename = $request->route()->getName();
        $gosearch  = '';
        $gocategor = '';
        $codalte   = '';

        if($routename == 'url.busqueda'){
            $id       = str_replace("'",'', $id);
            $gosearch = $id;
            $id = '';
        }

        if($routename == 'url.instancia'){
            $id       = str_replace("'",'', $id);
            $gocategor = $id;
            $id = '';
        }

        $path        = 'public';
        $view        = 'home/home';
        $data        = '';
        $destacados  = '';
        $marcaprod   = '';
        $categoria   = '';
        $promociones = '';
        $promo_dest  = [];
        $promo_dest2 = [];

        if(Auth::user() and auth()->user()->type != ''){

            if( auth()->user()->type == 'admin')
                return redirect('dashboard');

        }else{
            $promo_dest = Promocion::with(['producto'])->where(['activo' => 1, 'destacada' => 1])->where('imagen_home', '<>', '')->get()->take(4);
            $promo_dest2 = Promocion::with(['producto'])->where(['activo' => 1, 'destacada' => 2])->where('imagen_home', '<>', '')->get()->take(4);

        }

        $instPpales = Sainsta::where('destacada', 1)->orderBy('codalte')->get();
        $instancias = Sainsta::with(['productos', 'hijos']);
        $marcas     = Saprod::select('marca')->where('marca','<>','')->orderBy('marca')->groupBy('marca')->get();
        $vista    = "home.layouts.products.$path.grid";
        $precios  = '';
        $producto = '';
        $busqueda = '';

        if(isset($id) and $id > 0 ){
            $data = Saprod::with(['instancia'])->find($id);
            if(isset($data->id) and isset($data->instancia->codalte)){
                $codalte    =  $data->instancia->codalte;

                $length     = strlen($codalte);
                $instancias = $instancias->whereRaw(" left(codalte, $length) = '$codalte'");
            }else{
                if(!isset($data->instancia->codalte)){
                    // dd($data->instancia);
                }

            }
        }

        if(isset($request->categoria) and $request->categoria != '' and !isset($id)){
            $categoria  = $request->categoria;
            $cat        = Sainsta::where('codinst', $categoria)->first();

            $codalte    =  $cat->codalte;

            $length     = strlen("$codalte");
            $instancias = $instancias->whereRaw(" left(codalte, $length) = '$codalte'");
        }

        if(isset($request->marcaprod) and $request->marcaprod >0 and !isset($id)){
            $marcaprod  = $request->marcaprod;
        }

        $destacado   = ($id)? '1' : '22';
        $producto_id = $ordenprecio = $vertodos = 0;

        if(isset($request->vista) and $request->vista == 1)
            $vista     = "home.layouts.products.$path.list";

        if(isset($request->busqueda))
            $busqueda  = $request->busqueda;

        if(isset($request->vertodos))
            $vertodos = $request->vertodos;

        if(isset($request->ordenprecio))
            $ordenprecio = $request->ordenprecio;

        if(isset($request->destacado))
            $destacado = $request->destacado;

        if(isset($request->precios))
            $precios   = $request->precios;

        if(isset($request->id))
            $producto_id = $request->id;

        $productos = $this->buscar_producto($busqueda, $destacado, $precios, $marcaprod, $categoria, $producto_id, $ordenprecio, $vertodos);

        if(isset($busqueda) and $busqueda != ''  and isset($productos[0]) and !isset($request->categoria)){

            if(isset($productos[0]) and isset($productos[0]->instancia->padre->codalte))
                $codalte = explode('.', $productos[0]->instancia->padre->codalte );
            else
                if(isset($productos[0]) and isset($productos[0]->instancia->codalte))
                    $codalte = explode('.', $productos[0]->instancia->codalte );

            if(isset($codalte[0]))
                $codalte     = $codalte[0];

            $length      = strlen($codalte);
            $instancias  = $instancias->whereRaw(" left(codalte, $length) = '$codalte'");
        }

        $instancias = $instancias->orderBy('descrip')->get();

        $vista_prod = '';
        if(isset($id) and $id != ''){

            if($request->ajax()){
                $data = view('home.layouts.partials.producto', compact('data'))->render();
                return response()->json(['data' => $data], 200);
            }

            if(isset($data->id))
                $vista_prod = view('home.layouts.products.detail', compact('data', 'instancias'))->render();

        }else{
            if($request->route()->getCompiled()->getStaticPrefix() == '/busqueda')
                $vista_prod = view($vista, compact('productos', 'promociones', 'ordenprecio', 'vertodos'))->render();
        }

        if(!isset($productos[0])){
            $instancias = Sainsta::with(['productos', 'hijos'])->where('destacada', 1)->orderBy('descrip')->get();
        }

        if($request->ajax()){
            $vista_prod = view($vista, compact('productos', 'promociones', 'ordenprecio', 'vertodos'))->render();
            $marcasrend = view('home.layouts.partials.marcas',     compact('marcas'))    ->render();
            $instrender = view('home.layouts.partials.instancias', compact('instancias'))->render();
            return  response()->json(['marcas'     => $marcasrend,
                'instancias' => $instrender,
                'vista_prod' => $vista_prod,
                'count'      => count($productos),
                'nextPage'   => $productos->nextPageUrl()],200);
        }

        $noajax = 1;
        if(strlen($vista_prod)<1 and Auth()->user())
            $noajax = 11;

        return view($view , compact('noajax', 'marcas', 'routename', 'gocategor', 'gosearch', 'promo_dest','promo_dest2', 'instPpales', 'data', 'destacados', 'vista_prod', 'instancias', 'producto', 'productos'));

    }

    public function buscar_producto($busqueda, $destacado, $precios, $marcaprod, $categoria, $producto_id, $ordenprecio, $vertodos)
    {
        $productos   = Saprod::with(['instancia']);



        if($producto_id > 0 and is_numeric($producto_id)){
            $productos = $productos->where('id', $producto_id);
        }else{
            if(isset($precios['costod3'])){
                $precio2 = $precios['costod2'];
                if($precio2 == 500){
                    $precio2 = 100000000;
                }
                $productos = $productos->whereBetween('precio', [$precios['precio1'], $precio2]);
            }

            if($busqueda) {

                $sql = '';
                $busqueda = str_replace("'",'', $busqueda);

                $vector = explode(' ', $busqueda);
                foreach ($vector as $index => $item){

                    $iter   = $item;
                    $l_iter = strlen($iter);
                    $ultima = substr($iter, -1, 1);

                    if($ultima == 's' and $item != 'ups')
                        $iter = substr($iter, 0, $l_iter-1);

                    if($index > 0) {
                        $sql      .= " and ";
                    }
                    $sql      .= " ( descrip like '%$iter%'  or marca like '%$iter%' or refere like '%$iter%' or (codinst in (select codinst from sainsta where descrip like '%$iter%')) ) ";

                }
                $productos = $productos->whereRaw("( $sql )");


            }

            if($destacado == 1 and !$categoria and !$busqueda){
                $productos = $productos->where('destacado', $destacado);
            }

            if($categoria != ''){
                $cat       = Sainsta::where('codinst', $categoria)->first();
                $codalte = $cat->codalte;
                $len     = strlen($codalte);
                $productos = $productos->whereHas('instancia',
                    function ($query) use ($len, $codalte){
                        $query->whereRaw(" LEFT(codalte, $len) = '$codalte' ");
                    });
            }

        }


        if($busqueda and isset(auth()->user()->type) and auth()->user()->type == 'admin'  ) {
            $sql = '';
            $vector = explode(' ', $busqueda);
            foreach ($vector as $index => $item){
                if($index > 0) $sql .= " and ";
                $sql .= " ( descrip like '%$item%' or  imagen like '%$item%' or  imagen_home like '%$item%' ) ";
            }

        }


        $productos->where('existen','>','0');


        if($marcaprod){
            $productos->whereRaw("marca like'%$marcaprod%'");
        }

        if($ordenprecio == 1)
            $productos = $productos->orderBy('costod3', 'desc');
        else
            if($ordenprecio == 2)
                $productos = $productos->orderBy('costod3');
            else
                $productos = $productos->orderBy('descrip');

        if($vertodos) {

            $productos = $productos->paginate(100000);
        }else {
            $productos = $productos->paginate(12);
        }
        $this->productos  = [];

        return $productos;

    }
}
