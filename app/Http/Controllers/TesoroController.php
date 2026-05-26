<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TesoroController extends Controller
{

    public function index(Request $request)
    {

        if($request->ajax()){

            $monto      = $request->monto;
            $referencia = $request->referencia;
            $idReceptor = $request->idReceptor;

            //dd('monto'.$monto.' referencia'.$referencia. ' idreceptor'.$idReceptor);

            $jsonobj = '
				{
				    "referencia" : "'.$referencia.'",
				    "monto"      : "'.$monto.'",
					"idReceptor" : "'.$idReceptor.'",
				}
				 ';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL            => "https://tpmovil.bt.gob.ve/RestTesoro/com/services/P2P/conformacion",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $jsonobj,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            $response = json_decode($response);

            $status   = $response->status;
            $mensaje  = $response->mensaje;

            return response()->json(['success' => 'success', 'status' => $status, 'mensaje' => $mensaje  ], 200);

        }else{
            return view('tesoro' );
        }
    }
}
