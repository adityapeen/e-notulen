<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendant;
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
}
