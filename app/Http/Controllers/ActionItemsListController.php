<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\MSatker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;


class ActionItemsListController extends Controller
{
    public function index_admin(String $hashed_id)
    {
        $color = ['primary','info','success'];
        if($hashed_id == 'ALL'){
            $actions = ActionItems::withCount('evidences')->
            // where('due_date','>',now())->
            orderBy('status','ASC')->orderBy('due_date', 'ASC')->paginate(15);
            $count = ActionItems::select('action_items.status', DB::raw('count(status) as total'))->groupBy('status')->get();
        }
        else if($hashed_id == 'BPS'){
            $actions = ActionItems::withCount('evidences')->whereHas('note', function ($query){
                $query->where('team_id', NULL);
            })
            // ->where('due_date','>',now())
            ->orderBy('status','ASC')->orderBy('due_date', 'ASC')->paginate(15);
            $count = ActionItems::select('action_items.status', DB::raw('count(status) as total'))->whereHas('note', function ($query){
                $query->where('team_id', NULL);
            })->groupBy('status')->get();
        }
        else {
            $satker_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
            $actions = ActionItems::withCount('evidences')->whereHas('note', function ($query) use ($satker_id){
                $query->whereHas('team', function ($query) use ($satker_id){
                    $query->where('satker_id', $satker_id);
                });
            })
            // ->where('due_date','>',now())
            ->orderBy('status','ASC')->orderBy('due_date', 'ASC')->paginate(15);
            $count = ActionItems::select('action_items.status', DB::raw('count(status) as total'))->whereHas('note', function ($query) use ($satker_id){
                $query->whereHas('team', function ($query) use ($satker_id){
                    $query->where('satker_id', $satker_id);
                });
            })->groupBy('status')->get();
        }
        $title = "Action Items";
        $satkers = MSatker::all();
        return view('admin.action.index', compact(['title','actions','satkers','count','color']));
    }



    public function index_satker()
    {
        $title = "Action Items";
        $color = ['primary','info','success'];
        if(auth()->user()->level_id == 8){ // Admin Bidang
            $actions = ActionItems::withCount('evidences')->whereHas('note', function ($query){
                $query->where('team_id', auth()->user()->team_id);
            })
            // ->where('due_date','>',now())
            ->orderBy('status','ASC')->orderBy('due_date', 'ASC')->paginate(15);
            $count = ActionItems::select('action_items.status', DB::raw('count(status) as total'))->whereHas('note', function ($query){
                $query->where('team_id', auth()->user()->team_id);
            })->groupBy('status')->get();
        }
        else { // Admin Satker
            $actions = ActionItems::withCount('evidences')->whereHas('note', function ($query){
                $query->whereHas('team', function ($query){
                    $query->where('satker_id', auth()->user()->satker_id);
                });
            })
            // ->where('due_date','>',now())
            ->orderBy('status','ASC')->orderBy('due_date', 'ASC')->paginate(15);
            $count = ActionItems::select('action_items.status', DB::raw('count(status) as total'))->whereHas('note', function ($query){
                $query->whereHas('team', function ($query){
                    $query->where('satker_id', auth()->user()->satker_id);
                });
            })->groupBy('status')->get();
        }
        return view('satker.action.index', compact(['title','actions','count','color']));
    }

    public function index_ses(String $hashed_id)
    {
        $color = ['primary','info','success'];
        if($hashed_id == 'ALL'){
            $actions = ActionItems::withCount('evidences')->orderBy('status','ASC')
            // where('due_date','>',now())->
            ->orderBy('due_date', 'ASC')->paginate(15);
            $count = ActionItems::select('action_items.status', DB::raw('count(status) as total'))->groupBy('status')->get();
        }
        else if($hashed_id == 'BPS'){
            $actions = ActionItems::withCount('evidences')->whereHas('note', function ($query){
                $query->where('team_id', NULL);
            })
            // ->where('due_date','>',now())
            ->orderBy('status','ASC')->orderBy('due_date', 'ASC')->paginate(15);
            $count = ActionItems::select('action_items.status', DB::raw('count(status) as total'))->whereHas('note', function ($query){
                $query->where('team_id', NULL);
            })->groupBy('status')->get();
        }
        else {
            $satker_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
            $actions = ActionItems::withCount('evidences')->whereHas('note', function ($query) use ($satker_id){
                $query->whereHas('team', function ($query) use ($satker_id){
                    $query->where('satker_id', $satker_id);
                });
            })
            // ->where('due_date','>',now())
            ->orderBy('status','ASC')->orderBy('due_date', 'ASC')->paginate(15);
            $count = ActionItems::select('action_items.status', DB::raw('count(status) as total'))->whereHas('note', function ($query) use ($satker_id){
                $query->whereHas('team', function ($query) use ($satker_id){
                    $query->where('satker_id', $satker_id);
                });
            })->groupBy('status')->get();
        }
        $title = "Action Items";
        $satkers = MSatker::all();
        return view('observer.ses.action_index', compact(['title','actions','satkers','count','color']));
    }


}
