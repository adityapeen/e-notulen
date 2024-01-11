<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\Attendant;
use App\Models\MomRecipients;
use App\Models\Note;
use App\Models\Pic;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    //
    function attendants (String $hashed_id)
    {
        $note_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $attendants = Attendant::where(['note_id'=>$note_id])->get();
        $recipients = MomRecipients::where(['note_id'=>$note_id])->get();
        $res = array();
        $rec = array();
        foreach($attendants as $a)
        {
            $item = array(
                'id'    =>$a->user->id_hash(),
                'text'  => $a->user->name
            );
            array_push($res,$item);
        }
        foreach($recipients as $a)
        {
            $item = array(
                'id'    =>$a->user->id_hash(),
                'text'  => $a->user->name
            );
            array_push($rec,$item);
        }
        return json_encode(["results"=>[
            "attendants" => $res,
            "mom_recipients" => $rec
        ]]);
    }
    
    function group_attendants (String $hashed_id)
    {
        $agenda_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $attendants = UserGroup::where(['agenda_id'=>$agenda_id])->get();
        $res = array();
        foreach($attendants as $a)
        {
            $item = array(
                'id'    =>$a->user->id_hash(),
                'text'  => $a->user->name
            );
            array_push($res,$item);
        }
        return json_encode(["results"=>$res]);
    }

    function action_pic (String $hashed_id)
    {
        $action_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $pics = Pic::where(['action_id'=>$action_id])->get();

        $res = array();
        foreach($pics as $a)
        {
            $item = array(
                'id'    =>$a->user->id_hash(),
                'text'  => $a->user->name
            );
            array_push($res,$item);
        }
        return json_encode(["results"=>$res]);
    }

    function all_pic(String $hashed_id){
        $note_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $pics = Attendant::where(['note_id'=>$note_id])->get();

        $res = array();
        foreach($pics as $a)
        {
            $item = array(
                'id'    =>$a->user->id_hash(),
                'text'  => $a->user->name
            );
            array_push($res,$item);
        }
        return json_encode(["results"=>$res]);
    }

    public function note_detail(String $hashed_id)
    {
        $id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $item = Note::select('issues','file_notulen','name','date','status')->find($id);
        $note = array(
            'issues' => $item->issues,
            'name' => $item->name,
            'date' => $item->date,
            'file_notulen' => $item->status == 'lock' ? $item->file_notulen : NULL
        );
        $list = Attendant::where(['note_id'=>$id])->get();
        $attendants = array();
        foreach($list as $l){
            array_push($attendants,$l->user->name);
        }
        return compact(['note','attendants']);
    }

    public function action_item_detail(String $hashed_id)
    {
        $id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $item = ActionItems::find($id);
        $list = Pic::where('action_id', $id)->get();
        $pics = array();

        $action_item = [
            'name' => $item->note->name,
            'date' => $item->note->date,
            'what' => $item->what,
            'how' => $item->how,
            'due_date' => $item->due_date,
            'status' => $item->status
        ];

        foreach($list as $item){
            $data = [
                'name' => $item->user->name
            ];
            array_push($pics, $data);
        }

        return compact(['action_item','pics']);
    }

    public function check_api_wa()
    {
        $url_api = env('API_URL') == null ? 'http://localhost:8000' : env('API_URL');
        $url = $url_api."/check";

        $response = Http::post($url);
        return response($response);
    }
}
