<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\Attendant;
use App\Models\MomRecipients;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $att = Attendant::where(['note_id'=>$note_id])->get();
            $rec = MomRecipients::where(['note_id'=>$note_id])->get();
            
            $attendants = $att->map(function($a) {
                return [
                    'id' => $a->hashed_id,
                    'name' => $a->user->name,
                    'type' => 'a',
                ];
            })->toArray();

            $receivers = $rec->map(function($r) {
                return [
                    'id' => $r->hashed_id,
                    'name' => $r->user->name,
                    'type' => 'r',
                ];
            })->toArray();

            $recipients = array_merge($attendants, $receivers);
        }
        else{
            $status = false;
            $recipients = null;
        }
        return response()->json(['status'=>$status,'results'=>$recipients]);

    }

    public function send_individual_mom(String $hashed_id, String $type)
    {
        // Initial
        $arr_id = Hashids::decode($hashed_id);

        $status = false;
        $results = null;
        $res = null;

        // Validate Hashed ID
        if (!is_array($arr_id) || !isset($arr_id[0])) {
            Log::channel('daily')->error('MOM SEND FAILED', [
                'hashed_id' => $hashed_id,
                'type'      => $type,
                'response'  => 'Invalid hashed ID'
            ]);

            return response()->json([
                'status'   => $status,
                'results'  => null,
                'messages' => 'Invalid hashed ID'
            ]);
        }

        $attendance_id = $arr_id[0];
        
        // Get Attendance / Recipient        
        if ($type == "a") {
            $attendance = Attendant::find($attendance_id);
        } elseif ($type == "r") {
            $attendance = MomRecipients::find($attendance_id);
        } else {
            Log::channel('daily')->error('MOM SEND FAILED', [
                'attendance_id' => $attendance_id,
                'type'          => $type,
                'response'      => 'Invalid type'
            ]);
            
            return response()->json([
                'status'   => $status,
                'results'  => null,
                'messages' => 'Invalid type'
            ]);
        }

        
        // Validate Attendance
        if (!$attendance) {
            Log::channel('daily')->error('MOM SEND FAILED', [
                'attendance_id' => $attendance_id,
                'type'          => $type,
                'response'      => 'Attendance / recipient not found'
            ]);

            return response()->json([
                'status'   => $status,
                'results'  => null,
                'messages' => 'Attendance / recipient not found'
            ]);
        }

        
        // | Validate User
        if (!$attendance->user) {
            Log::channel('daily')->error('MOM SEND FAILED', [
                'attendance_id' => $attendance_id,
                'type'          => $type,
                'response'      => 'User not found'
            ]);

            return response()->json([
                'status'   => $status,
                'results'  => null,
                'messages' => 'User not found'
            ]);
        }

        
        // Get Note
        $notes = Note::where('id', $attendance->note_id)->first();

        if (!$notes) {
            Log::channel('daily')->error('MOM SEND FAILED', [
                'attendance_id' => $attendance_id,
                'type'          => $type,
                'note_id'       => $attendance->note_id,
                'response'      => 'Note not found'
            ]);

            return response()->json([
                'status'   => $status,
                'results'  => null,
                'messages' => 'Note not found'
            ]);
        }

        
        // | Basic Data        
        $date = date_create($notes->date);
        $file_location = 'notulensi/' . $notes->file_notulen;
        $phone = $attendance->user->phone;
        $api_id = $type . ';' . $attendance->hashed_id;

        
        // Check Eligibility
        if (
            $attendance->user->current_role_id <= 1 ||
            $attendance->mom_sent !== null ||
            $phone === '-'
        ) {

            $results = $attendance->user->name . " - SKIP";

            Log::channel('daily')->info('MOM SEND SKIP', [
                'attendance_id' => $attendance_id,
                'type'          => $type,
                'user'          => $attendance->user->name,
                'phone'         => $phone,
                'reason'        => 'Not eligible for sending'
            ]);

            return response()->json([
                'status'   => $status,
                'results'  => $results,
                'messages' => $results
            ]);
        }


        // START LOG
        Log::channel('daily')->info('MOM SEND START', [
            'attendance_id' => $attendance_id,
            'type'          => $type,
            'user'          => $attendance->user->name,
            'phone'         => $phone,
            'note_id'       => $notes->id,
            'note_name'     => $notes->name,
            'api_id'        => $api_id,
            'has_file'      => !empty($notes->file_notulen)
        ]);


        // Send Message
        try {

            // Without File
            if ($notes->file_notulen == null) {

                $message = "Berikut ini kami sampaikan notulen *"
                    . $notes->name
                    . "* pada tanggal "
                    . date_format($date, "d-m-Y")
                    . ". Silahkan akses notulen pada link berikut : \n"
                    . $notes->link_drive_notulen
                    . "\nTerimakasih 🙏🙏🙏";

                Log::channel('daily')->info('MOM SEND REQUEST', [
                    'attendance_id' => $attendance_id,
                    'type'          => $type,
                    'api_id'        => $api_id,
                    'mode'          => 'message'
                ]);

                $response = Http::timeout(240)
                    ->withBasicAuth(
                        env('API_USER'),
                        env('API_PASSWORD')
                    )
                    ->post(
                        $this->url . '/send-message',
                        [
                            'number'  => $phone,
                            'message' => $message,
                            'id'      => $api_id
                        ]
                    );
            }

            // With File
            else {

                // Check File
                if (!file_exists($file_location)) {

                    $results = $attendance->user->name . " - FAIL";

                    Log::channel('daily')->error('MOM SEND FAILED', [
                        'attendance_id' => $attendance_id,
                        'type'          => $type,
                        'user'          => $attendance->user->name,
                        'file'          => $file_location,
                        'response'      => 'File not found'
                    ]);

                    return response()->json([
                        'status'   => $status,
                        'results'  => $results,
                        'messages' => 'File not found'
                    ]);
                }

                $message = "Berikut ini kami sampaikan notulen *"
                    . $notes->name
                    . "* pada tanggal "
                    . date_format($date, "d-m-Y")
                    . ". \n"
                    . "\nTerimakasih 🙏🙏🙏";

                Log::channel('daily')->info('MOM SEND REQUEST', [
                    'attendance_id' => $attendance_id,
                    'type'          => $type,
                    'api_id'        => $api_id,
                    'mode'          => 'file',
                    'file'          => $file_location
                ]);

                $response = Http::timeout(240)
                    ->withBasicAuth(
                        env('API_USER'),
                        env('API_PASSWORD')
                    )
                    ->attach(
                        'file',
                        file_get_contents($file_location),
                        $notes->file_notulen
                    )
                    ->post(
                        $this->url . '/send-message',
                        [
                            'number'  => $phone,
                            'message' => $message,
                            'id'      => $api_id
                        ]
                    );
            }

            // Get Raw Response
            $raw_response = $response->body();

            // Decode Response
            $res = json_decode($raw_response);

            // JSON Decode Validation
            if (json_last_error() !== JSON_ERROR_NONE) {

                $results = $attendance->user->name . " - FAIL";

                Log::channel('daily')->error('MOM SEND FAILED', [
                    'attendance_id' => $attendance_id,
                    'type'          => $type,
                    'user'          => $attendance->user->name,
                    'http_status'   => $response->status(),
                    'response'      => $raw_response,
                    'json_error'    => json_last_error_msg()
                ]);

                return response()->json([
                    'status'   => $status,
                    'results'  => $results,
                    'messages' => $raw_response
                ]);
            }

            // API SUCCESS
            
            // API sekarang hanya mengembalikan:
            // {
            //      "status": true
            // }
            
            if (
                isset($res->status) &&
                $res->status === true
            ) {

                $results = $attendance->user->name . " - OK";

                // Update Database
                // message_id dihapus karena API sudah tidak mengembalikannya.
            
                $attendance->update([
                    'mom_sent' => now()
                ]);

                // SUCCESS LOG
                Log::channel('daily')->info('MOM SEND', [
                    'attendance_id' => $attendance_id,
                    'type'          => $type,
                    'user'          => $attendance->user->name,
                    'result'        => 'SUCCESS'
                ]);

                return response()->json([
                    'status'   => true,
                    'results'  => $results,
                    'messages' => $res
                ]);
            }

            // API FAILED
            $results = $attendance->user->name . " - FAIL";

            // Convert decoded response to string
            $decoded_response = json_encode(
                $res,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
            );

            // FAILED LOG
            // Menyimpan seluruh response API
            Log::channel('daily')->error('MOM SEND', [
                'attendance_id' => $attendance_id,
                'type'          => $type,
                'user'          => $attendance->user->name,
                'result'        => $decoded_response
            ]);

            return response()->json([
                'status'   => $status,
                'results'  => $results,
                'messages' => $res
            ]);

        } catch (\Throwable $e) {

            // Exception / Timeout / Connection Error
            $status = false;

            $results = $attendance->user->name . " - FAIL";

            Log::channel('daily')->error('MOM SEND', [
                'attendance_id' => $attendance_id,
                'type'          => $type,
                'user'          => $attendance->user->name,
                'result'        => $e->getMessage()
            ]);

            return response()->json([
                'status'   => $status,
                'results'  => $results,
                'messages' => $e->getMessage()
            ]);
        }
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

    public function delete_message(String $id, String $type){
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
        
        $number = $attendance->user->phone;
        $message_id = $attendance->message_id;

        $response = Http::withBasicAuth(env('API_USER'), env('API_PASSWORD'))->post($this->url.'/delete-message', [
            'number' => $number,
            'messageId' => $message_id,
        ]);

        $res = json_decode($response);

        if($attendance != null && $attendance->update(['mom_sent' => null]))
            return response()->json([
                                'status' => true,
                                'messages' => ['Status telah direset', $res->message]]);
        else
            return response()->json(['status' => false, 'message' => "Not Found"], 404);
    }

    public function get_user_profile(Request $request, String $number){
        //The $number is sent using base64 format
        if(!$this->checkAuthHeader($request->header('Authorization')))
        {
            return response('Unauthorized', 401);
        }

        $status = 'OK';
        $message = "";
        $phone = base64_decode($number);
        $user = User::where("phone", $phone)->first();

        if($user == null)  {
            $status = "Not Found";
            $message = "Nomor Anda tidak terdaftar pada database kami";
        }
        else {
            $message = "Halo ".$user->name.", anda terdaftar dengan email : ".$user->email
            ."\nSilahkan login menggunakan email tersebut pada ".url('/')
            ."\nPassword default anda adalah 12345"
            ."\n\n_With ❤  Bot_BPSDM_";
        }

        return response()->json([
            'status'=>$status,
            'message' => $message]);        
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
