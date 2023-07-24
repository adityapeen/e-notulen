<?php

namespace App\Http\Controllers;

use App\Models\ActionItems;
use App\Models\Agenda;
use App\Models\MSatker;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('home');
        $title = "Dashboard";
        if(auth()->user()->level_id == 1 || auth()->user()->level_id == 2){ // Ka & Timstra
            $users = User::all()->count();
            $wa_ready = User::whereNot('phone','')->count();
            $agendas = Agenda::all()->count();
            $notes = Note::all()->count();
            $notes_satkers = MSatker::withCount('notes')->get();
            $notes_locked = Note::where('status','lock')->count();
            $actions = ActionItems::all()->count();
            $actions_todo = ActionItems::where('status','todo')->count();
            $actions_progress = ActionItems::where('status','onprogress')->count();
            $undone = ActionItems::whereNot('status','done')
                                ->whereHas('note', function ($query) {
                                    $query->where('team_id', NULL);
                                })->get();
            $todays = Note::where('date', date('Y-m-d'))->get();
            return view('admin.notulen', compact(['users','agendas','notes','actions','todays','title','wa_ready','notes_locked','actions_todo','actions_progress','undone','notes_satkers']));
        }
        else if (auth()->user()->level_id < 9 ){
            $users = User::where('satker_id', auth()->user()->satker_id)->count();
            $wa_ready = User::where('satker_id', auth()->user()->satker_id)->whereNot('phone','')->count();
            $agendas = Agenda::where('satker_id', auth()->user()->satker_id)->count();
            $notes = Note::whereHas('team', function ($query) {
                        $query->where('satker_id', auth()->user()->satker_id);
                    })->count();
            $notes_locked = Note::where('status','lock')
                    ->whereHas('team', function ($query) {
                        $query->where('satker_id', auth()->user()->satker_id);
                    })->count();
            $actions = ActionItems::whereHas('note.team', function ($query) {
                        $query->where('satker_id', auth()->user()->satker_id);
                    })->count();
            $actions_todo = ActionItems::where('status','todo')
                    ->whereHas('note.team', function ($query) {
                        $query->where('satker_id', auth()->user()->satker_id);
                    })->count();
            $actions_progress = ActionItems::where('status','onprogress')
                    ->whereHas('note.team', function ($query) {
                        $query->where('satker_id', auth()->user()->satker_id);
                    })->count();
            if(auth()->user()->level_id == 7){
                $undone = ActionItems::where('status', '<>', 'done')
                        ->whereHas('note.team', function ($query) {
                            $query->where('satker_id', auth()->user()->satker_id);
                        })->get();
                    }
            else{
                $undone = ActionItems::where('status', '<>', 'done')
                        ->whereHas('note.team', function ($query) {
                            $query->where('team_id', auth()->user()->team_id);
                        })->get();

            }
            $todays = Note::select('notes.*','attendants.user_id')
            ->join('attendants', 'notes.id', '=', 'attendants.note_id')
            ->where('attendants.user_id',auth()->user()->id)
            ->where('date', date('Y-m-d'))->get();
            return view('satker.notulen', compact(['users','agendas','notes','actions','todays','title','wa_ready','notes_locked','actions_todo','actions_progress','undone']));
        }
        else{
            $undone = ActionItems::with('pics')->where('status','done')->get();
            $todays = Note::select('notes.*','attendants.user_id')
            ->join('attendants', 'notes.id', '=', 'attendants.note_id')
            ->where('attendants.user_id',auth()->user()->id)
            ->where('date', date('Y-m-d'))->get();
            return view('user.notulen', compact(['todays','title']));
        }
    }
}
