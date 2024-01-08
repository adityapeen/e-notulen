<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\Attendant;
use App\Models\Comment;
use App\Models\Evidence;
use App\Models\Note;
use App\Models\Pic;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class UserNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Daftar Notulensi";
        $notes = Note::join('attendants', 'notes.id', '=', 'attendants.note_id')
        ->where('attendants.user_id',auth()->user()->id)
        ->where('type','public')
        ->select('notes.*')
        ->orderBy('date', 'DESC')->paginate(15);
        return view('user.note.index', compact(['notes','title']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(String $hashed_id)
    {
        $title = "Notulensi";
        $note_id = Hashids::decode($hashed_id)[0];
        $note = Note::findOrFail($note_id);
        return view('user.note.view', compact(['title','note']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function action_items(String $hashed_id)
    {
        $title = "Action Items";
        $note_id = Hashids::decode($hashed_id)[0];
        $note = Note::findOrFail($note_id);
        $attendants = Attendant::where('note_id',$note_id)->get();
        $actions = ActionItems::withCount(['evidences'])->where('note_id',$note_id)->get();
        // $actions = User::find(auth()->user()->id)->pics;
        return view('user.note.action_view', compact(['title','note','attendants','actions']));
    }

    public function evidence(String $hashed_id){
        $title = "Eviden Action Item";
        $action_id = Hashids::decode($hashed_id)[0];
        $action = ActionItems::findOrFail($action_id);
        $evidences = Evidence::where('action_id', $action_id)->get();
        $pics = Pic::where('action_id', $action_id)->get();
        $comments = Comment::where('action_id', $action_id)->count();
        return view('user.evidence.index', compact(['title','action','evidences','pics','comments']));
    }
}
