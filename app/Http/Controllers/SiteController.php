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
        if(isset($request->email)) {
            echo 'mail';
            Mail::raw('Prueba de correo', function ($message) {
                $message->to('geal16ster@gmail.com')->subject('Prueba');
            });
        }
        if(Auth::user() and auth()->user()->type == 'admin'){
            return response()->redirectTo('/admin');
        }
        return view("home.home");
    }
}
