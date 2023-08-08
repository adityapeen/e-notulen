<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Vinkla\Hashids\Facades\Hashids;

class WABlastController extends Controller
{
    protected $url;
    public function __construct()
    {
        $this->url = env('API_URL') == NULL ? 'http://localhost:8000' : env('API_URL') ;
    }

    public function index()
    {
        $title = "WhatsApp Blast";
        $recipients = User::whereNot('phone', '')->get();
        return view('admin.wa-blast', compact(['title','recipients']));
    }

    public function send_blast(Request $request)
    {
        $status = true;
        $message = $request->message;
        $recipient = Hashids::decode($request->recipients);
        if(!is_array($recipient)){ // Not Valid ID
            return response()->json(['status'=>'fail','messages'=>'Invlaid IDs']);
        }

        $user = User::find($recipient[0]);   

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

        return response()->json(['status'=>$status,'results'=>$results,'messages'=>$res]);
    }
}
