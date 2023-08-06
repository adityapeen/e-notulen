<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\MSatker;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;


class ActionItemsListController extends Controller
{
    public function index_admin(String $hashed_id)
    {
        if($hashed_id == 'ALL'){
            $actions = ActionItems::orderBy('id', 'DESC')->paginate(15);
        }
        else if($hashed_id == 'BPS'){
            $actions = ActionItems::whereHas('note', function ($query){
                $query->where('team_id', NULL);
            })->orderBy('id', 'DESC')->paginate(15);
        }
        else {
            $satker_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
            $actions = ActionItems::whereHas('note', function ($query) use ($satker_id){
                $query->whereHas('team', function ($query) use ($satker_id){
                    $query->where('satker_id', $satker_id);
                });
            })->orderBy('id', 'DESC')->paginate(15);
        }
        $title = "Action Items";
        $satkers = MSatker::all();
        return view('admin.action.index', compact(['title','actions','satkers']));
    }



    public function index_satker()
    {
        $title = "Action Items";
        if(auth()->user()->level_id == 8){ // Admin Bidang
            $actions = ActionItems::select()->whereHas('note', function ($query){
                $query->where('team_id', auth()->user()->team_id);
            })
            ->orderBy('id', 'DESC')->paginate(15);
        }
        else { // Admin Satker
            $actions = ActionItems::whereHas('note', function ($query){
                $query->whereHas('team', function ($query){
                    $query->where('satker_id', auth()->user()->satker_id);
                });
            })
            ->orderBy('id', 'DESC')->paginate(15);
        }
        
        return view('satker.action.index', compact(['title','actions']));
    }


}
