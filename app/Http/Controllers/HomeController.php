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
        $users = User::all()->count();
        $agendas = Agenda::all()->count();
        $notes = Note::all()->count();
        $actions = ActionItems::all()->count();
        return view('admin.notulen', compact(['users','agendas','notes','actions']));
    }
}
