<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;

class SiteController extends Controller
{
    public $promociones;

    public function __construct()
    {
        //$this->middleware('auth')->except(['webhooks','index','panelclientes', 'promo', 'promoid', 'pago', 'procesar', 'encoded', 'tokencsrf', 'promociones', 'gourl', 'gourlpromo']);
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


    public function encoded(Request $request)
    {
        $msg = $request->msg;
        $msg = 'https://wa.me/584123268315?text='.urlencode($msg);

        return response()->json( $msg, 200);
    }





    public function index(Request $request, $id = null)
    {
        //dd(bcrypt('Tucani$214'));

        Mail::raw('Prueba de correo', function($message) {
            $message->to('geal16ster@gmail.com')->subject('Prueba');
        });
        return view("home.home");
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
