<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\Attendant;
use App\Models\MomRecipients;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;

class MoMController extends Controller
{
    protected $url;
    public function __construct()
    {
        $this->url = env('API_URL') == NULL ? 'http://localhost:8000' : env('API_URL') ;
    }

    public function mom_recipient(String $hashed_id){
        $arr_id = Hashids::decode($hashed_id);
        $status = true;
        if(is_array($arr_id)){
            $note_id = $arr_id[0];
            $attendants = Attendant::join('users', 'attendants.user_id', '=', 'users.id')
            ->select('attendants.id', 'users.name', DB::raw('"a" as type'))
            ->where(['note_id'=>$note_id])->get()->toArray();
            $receivers = MomRecipients::join('users', 'mom_recipients.user_id', '=', 'users.id')
            ->select('mom_recipients.id', 'users.name', DB::raw('"r" as type'))
            ->where(['note_id'=>$note_id])->get()->toArray();
            $recipients = array_merge($attendants, $receivers);
        }
        else{
            $status = false;
            $recipients = null;
        }
        return response()->json(['status'=>$status,'results'=>$recipients]);

    }

    public function send_individual_mom(String $hashed_id, String $type){
        $arr_id = Hashids::decode($hashed_id);
        $status = true;
        if(is_array($arr_id)){ //Valid ID
            $attendance_id = $arr_id[0];
            if($type == "a"){
                $attendance = Attendant::find($attendance_id);
            }
            else if($type == "r"){
                $attendance = MomRecipients::find($attendance_id);
            }
            else{
                $status = false;
                $results = null;
                return response()->json(['status'=>$status,'results'=>$results,'messages'=>NULL]);
            }
            $notes = Note::where('id',$attendance->note_id)->first();
            $date = date_create($notes->date);
            $file_location = 'notulensi/'.$notes->file_notulen;
            
            if($notes->file_notulen == NULL && $attendance->user->current_role_id > 2 && $attendance->mom_sent == NULL && $attendance->user->phone !== '-'){
                $message = "Berikut ini kami sampaikan notulen *"
                    .$notes->name."* pada tanggal *".date_format($date,"d-m-Y").".* Silahkan akses notulen pada link berikut : \n"
                    .$notes->link_drive_notulen
                    ."\nTerimakasih 🙏🙏🙏";

                $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($this->url.'/send-message', [
                    'number' => $attendance->user->phone,
                    'message' => $message,
                    'id' => $type.';'.$attendance->id
                ]);
            }
            else if($attendance->user->current_role_id > 2 && $attendance->mom_sent == NULL && $attendance->user->phone !== '-'){
                $message = "Berikut ini kami sampaikan notulen *"
                    .$notes->name."* pada tanggal *".date_format($date,"d-m-Y").".* \n"
                    ."\nTerimakasih 🙏🙏🙏";

                $response = Http::timeout(240)->withBasicAuth(env('API_USER'), env('API_PASSWORD'))
                                ->attach('file', file_get_contents($file_location),$notes->file_notulen)->post($this->url.'/send-message', [
                    'number' => $attendance->user->phone,
                    'message' => $message,
                    'id' => $type.';'.$attendance->id
                ]);
            }
            else{
                $results = $attendance->user->name." - SKIP";
                $status = false;
                return response()->json(['status'=>$status,'results'=>$results,'messages'=>$results]);
            }

            $res = json_decode($response);
            if($res->status){
                $results = $attendance->user->name." - OK";
                $attendance->update(['mom_sent'=>date('Y-m-d h:i:s')]);
            }
            else{
                $results = $attendance->user->name." - FAIL";
                $status = false;
            }
        }
        else{
            $status = false;
            $results = null;
        }
        return response()->json(['status'=>$status,'results'=>$results,'messages'=>$res]);
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

    public function update_mom_status(Request $request, String $id, String $type){
        if(!$this->checkAuthHeader($request->header('Authorization')))
        {
            return response('Unauthorized', 401);
        }

        $attendance_id = Hashids::decode($id)[0];

        if($type == "a"){
            $attendance = Attendant::find($attendance_id);
        }
        else if($type == "r"){
            $attendance = MomRecipients::find($attendance_id);
        }
        else{
            $status = false;
            $results = null;
            return response()->json(['status'=>$status,'results'=>$results,'messages'=>NULL], 404);
        }       
        
        if($attendance != null && $attendance->update(['mom_sent'=>date('Y-m-d h:i:s')]))
            return response()->json(['status'=>'OK']);
        else
            return response()->json(['status'=>false], 500);
    }

    private function checkAuthHeader($token){
        $username = env('API_USER');
        $password = env('API_PASSWORD');
        $key = 'Basic '.base64_encode($username.':'.$password);

        if($token == $key) 
            return true;
        else 
            return false;        
    }
}
