<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendant;
use App\Models\Note;
use App\Models\Pic;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class ApiController extends Controller
{
    //
    function attendants (String $hashed_id)
    {
        $note_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $attendants = Attendant::where(['note_id'=>$note_id])->get();
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
}
