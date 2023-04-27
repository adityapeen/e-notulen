<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\Attendant;
use App\Models\Note;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;

class MoMController extends Controller
{
    protected $url;
    public function __construct()
    {
        $this->url = env('API_URL') == NULL ? 'http://localhost:8000' : env('API_URL') ;
    }
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
       
        $file_location = 'notulensi/'.$notes->file_notulen;
        foreach($recipients as $r){
            if($notes->file_notulen == NULL){
                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($this->url.'/send-message', [
                    'number' => $r->user->phone,
                    'message' => $message,
                ]);
            }
            else{
                $message = "Berikut ini kami sampaikan notulen *"
                    .$notes->name."* pada tanggal *".date_format($date,"d-m-Y").".* \n"
                    ."\nTerimakasih 🙏🙏🙏";

                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))
                                ->attach('file', file_get_contents($file_location),$notes->file_notulen)->post($this->url.'/send-message', [
                    'number' => $r->user->phone,
                    'message' => $message,
                ]);
                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($this->url.'/send-message', [
                    'number' => $r->user->phone,
                    'message' => $message,
                ]);
            }
            // response()->json(file_get_contents($file_location));
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

    public function test_file(){
        $note_id = 8;
        $notes = Note::where('id',$note_id)->first();
        $date = date_create($notes->date);
        $message = "Berikut ini kami sampaikan notulen *"
                    .$notes->name."* pada tanggal *".date_format($date,"d-m-Y").".* Silahkan akses notulen pada link berikut : \n"
                    .$notes->link_drive_notulen
                    ."\nTerimakasih 🙏🙏🙏";
        $recipients = Attendant::where(['note_id'=>$note_id])->get();

        $report = array();
        $fail = array();
        $file_location = 'notulensi/'.$notes->file_notulen;

        foreach($recipients as $r){
            if($notes->file_notulen == NULL){
                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($this->url.'/send-message', [
                    'number' => $r->user->phone,
                    'message' => $message,
                ]);
            }
            else{
                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))
                                ->attach('file', file_get_contents($file_location),$notes->file_notulen)->post($this->url.'/send-message', [
                    'number' => $r->user->phone,
                    'message' => $message,
                ]);
                echo $response;
                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($this->url.'/send-message', [
                    'number' => $r->user->phone,
                    'message' => $message,
                ]);
            }
           
            $res = json_decode($response);
            
            array_push($report, $res->status);
            if(!$res->status){
                array_push($fail, $r->user->name);
            }
        }
    }

    public function send_reminder(){
        $action_items = ActionItems::join('pics','action_items.id','=','pics.action_id')
        ->join('users','pics.user_id','=','users.id')
        ->select('action_items.*', 'users.name', 'users.phone')
        ->where('action_items.status','todo')->get();

        $report = array();
        $fail = array();

        foreach($action_items as $item){
            $datediff = strtotime($item->due_date) - time();
            $sisa = round($datediff / (60 * 60 * 24)); // selisih dalam hari

            if($sisa == 3){
                $message = "Berikut ini kami sampaikan pengingat terhadap Action Item *"
                    .$item->note->name."* pada tanggal *".date_format(date_create($item->note->date),"d-m-Y").".*" 
                    ."\n\n*What* "
                    .wa_text($item->what)
                    ."\n\n*How* " 
                    .wa_text($item->how)
                    ."\n\n*Dateline ".date_format(date_create($item->due_date),"d-m-Y")."*"
                    // ."\nTerimakasih 🙏🙏🙏"
                    // ."\n*#".Hashids::decode($item->id)[0]."*"
                    // ."\n*#".$item->id."* "
                    ."\n\n_with ♥  Bot_BPSDM_"
                    ;

                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($this->url.'/send-message', [
                    'number' => $item->phone,
                    'message' => $message,
                ]);
                $res = json_decode($response);

                array_push($report, $res->status);
                if(!$res->status){
                    array_push($fail, $item->name);
                }
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
