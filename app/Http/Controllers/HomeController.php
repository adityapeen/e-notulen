<?php

namespace App\Http\Controllers;

use App\Models\ActionItems;
use App\Models\Agenda;
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
            $notes_locked = Note::where('status','lock')->count();
            $actions = ActionItems::all()->count();
            $actions_todo = ActionItems::where('status','todo')->count();
            $actions_progress = ActionItems::where('status','onprogress')->count();
            $undone = ActionItems::whereNot('status','done')->get();
            $todays = Note::where('date', date('Y-m-d'))->get();
            return view('admin.notulen', compact(['users','agendas','notes','actions','todays','title','wa_ready','notes_locked','actions_todo','actions_progress','undone']));
        }
        else{
            $todays = Note::select('notes.*','attendants.user_id')
            ->join('attendants', 'notes.id', '=', 'attendants.note_id')
            ->where('attendants.user_id',auth()->user()->id)
            ->where('date', date('Y-m-d'))->get();
            return view('user.notulen', compact(['todays','title']));
        }
    }
}
