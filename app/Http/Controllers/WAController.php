<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Vinkla\Hashids\Facades\Hashids;

class WAController extends Controller
{
    protected $url;
    public function __construct()
    {
        $this->url = env('API_URL') == NULL ? 'http://localhost:8000' : env('API_URL') ;
    }

    public function send_message($request)
    {
        $status = true;
        $message = wa_text($request->message);
        $user = User::find($request->recipient);   

        $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($this->url.'/send-message', [
            'number' => $user->phone,
            'message' => $message,
        ]);
        $res = json_decode($response);
        if($res->status){
            $results = $user->name." - OK";
        }
        else{
            $status = false;
            $results = $user->name." - FAIL";
        }

        $response_data = ['status'=>$status,'results'=>$results,'messages'=>$res];

        return $response_data;
    }
}
