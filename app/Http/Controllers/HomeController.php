<?php

namespace App\Http\Controllers;

use App\Models\ActionItems;
use App\Models\Agenda;
use App\Models\MSatker;
use App\Models\Note;
use App\Models\User;
use Spatie\Permission\Models\Role;
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
        // $user = User::find(auth()->user()->id);
        // if(auth()->user()->level_id == 1 || auth()->user()->level_id == 2){ // Ka & Timstra
        if(auth()->user()->current_role_id == 1 || auth()->user()->current_role_id == 2){ // Ka & Timstra
            // $user->assignRole(['Superadmin', 'Pegawai']);
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
                                })
                                ->orderBy('due_date', 'ASC')->paginate(10);
            $todays = Note::where('date', date('Y-m-d'))->get();
            return view('admin.notulen', compact(['users','agendas','notes','actions','todays','title','wa_ready','notes_locked','actions_todo','actions_progress','undone','notes_satkers']));
        }
        else if(auth()->user()->current_role_id == 3){
            return redirect()->route('ses.dashboard');
        }
        else if (auth()->user()->current_role_id < 9 ){
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
            if(auth()->user()->current_role_id == 7){
                $undone = ActionItems::where('status', '<>', 'done')
                        ->whereHas('note.team', function ($query) {
                            $query->where('satker_id', auth()->user()->satker_id);
                        })->orderBy('due_date', 'ASC')->get();
                $todays = Note::select('notes.*')
                        ->whereHas('team', function($query){
                            $query->where('satker_id', auth()->user()->satker_id);
                        })->where('date', date('Y-m-d'))->get();
            }
            else{
                $undone = ActionItems::where('status', '<>', 'done')
                        ->whereHas('note.team', function ($query) {
                            $query->where('team_id', auth()->user()->team_id);
                        })->orderBy('due_date', 'ASC')->get();
                $todays = Note::select('notes.*')
                        ->where('team_id', auth()->user()->team_id)
                        ->where('date', date('Y-m-d'))->get();
            }
            
            return view('satker.notulen', compact(['users','agendas','notes','actions','todays','title','wa_ready','notes_locked','actions_todo','actions_progress','undone']));
        }
        else{
            $undone = ActionItems::with('pics')->whereNot('status','done')
                                    ->whereHas('pics', function($query){
                                        $query->where('user_id', auth()->user()->id);
                                    })->orderBy('due_date', 'ASC')->get();
            $todays = Note::select('notes.*','attendants.user_id')
            ->join('attendants', 'notes.id', '=', 'attendants.note_id')
            ->where('attendants.user_id',auth()->user()->id)
            ->where('date', date('Y-m-d'))->get();
            return view('user.notulen', compact(['todays','title','undone']));
        }
    }
}
