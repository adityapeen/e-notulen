<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendant;
use App\Models\Note;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;

class MoMController extends Controller
{
    public function send_mom(String $hashed_note_id)
    {
        $note_id = Hashids::decode($hashed_note_id)[0];
        $notes = Note::where('id',$note_id)->first();
        $date = date_create($notes->date);
        $message = "Berikut ini kami sampaikan notulen *"
                    .$notes->name."* pada tanggal *".date_format($date,"d-m-Y").".* Silahkan akses notulen pada link berikut : \n"
                    .$notes->link_drive_notulen
                    ."\nTerimakasih 🙏🙏🙏";
        $recipients = Attendant::where(['note_id'=>$note_id])->get();

        $report = array();
        $fail = array();

        foreach($recipients as $r){
            $url = env('API_URL') == NULL ? 'http://localhost:8000' : env('API_URL') ;
            $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($url.'/send-message', [
                'number' => $r->user->phone,
                'message' => $message,
            ]);
            // $response = json_encode(['url'=>$url, "number"=>$r->user->phone, "message"=>$message]);
            $res = json_decode($response);
            
            array_push($report, $res->status);
            if(!$res->status){
                array_push($fail, $r->user->name);
            }
        }
        
        if(in_array(false, $report)){
            $status = false;
        }
        else{
            $status = true;
        }
        return response()->json(['status'=>$status,'fail'=>$fail]);
    }
}
