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
            $agendas = Agenda::all()->count();
            $notes = Note::all()->count();
            $actions = ActionItems::all()->count();
            $todays = Note::where('date', date('Y-m-d'))->get();
            return view('admin.notulen', compact(['users','agendas','notes','actions','todays','title']));
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
