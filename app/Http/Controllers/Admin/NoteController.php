<?php

namespace App\Http\Controllers\Admin;

use App\Models\Note;
use App\Models\Agenda;
use App\Http\Controllers\Controller;
use App\Models\Attendant;
use App\Models\User;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Daftar Notulensi";
        $notes = Note::all();
        return view('admin.note.index', compact(['notes','title']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Buat Notulensi";
        $agendas = Agenda::all();
        $users = User::all();
        $types = (object) [(object)['id'=>'public', 'name'=>'Publik'],(object)['id'=>'internal','name'=>'Internal']];
        return view('admin.note.create', compact(['agendas','types','title','users']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'agenda_id' => ['required'],
            'type' => ['required'],
            'name' => ['required'],
            'date' => ['required'],
        ]);
        $notes = Note::updateOrCreate([
                'agenda_id' => Hashids::decode($request->agenda_id)[0],
                'type' => $request->type,
                'name' => $request->name,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'max_execute' => $request->max_execute,
                'issues' => $request->issues,
                'link_drive_notulen' => $request->link_drive_notulen,
                'status' => 'open',
                'created_by' => auth()->user()->id,
        ]);
        if($notes){
            if(sizeof($request->attendants)>0){
                foreach( $request->attendants as $a){
                    Attendant::updateOrCreate([
                        'note_id'=>Hashids::decode($notes->id)[0],
                        'user_id'=> Hashids::decode($a)[0]
                    ]);
                }
            }
            return redirect()->route("admin.notes.index")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function edit(String $hashed_id)
    {
        $title = "Edit Notulensi";
        $id = Hashids::decode($hashed_id); //decode the hashed id
        $note = Note::find($id[0]);
        $agendas = Agenda::all();
        $users = User::all();
        $types = (object) [(object)['id'=>'public', 'name'=>'Publik'],(object)['id'=>'internal','name'=>'Internal']];

        return view('admin.note.edit', compact('title','types','agendas','note','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, String $hashed_id)
    {
        $request->validate([
            'agenda_id' => ['required'],
            'type' => ['required'],
            'name' => ['required'],
            'date' => ['required'],
        ]);
        $id = Hashids::decode($hashed_id);
        $notes = Note::findOrFail($id)->first();
        $notes->update([
            'agenda_id' => Hashids::decode($request->agenda_id)[0],
            'type' => $request->type,
            'name' => $request->name,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_execute' => $request->max_execute,
            'issues' => $request->issues,
            'link_drive_notulen' => $request->link_drive_notulen,
            'status' => 'open',
            'updated_by' => auth()->user()->id,
        ]) ;
        if($notes){
            if(sizeof($request->attendants)>0){
                $note_id = Hashids::decode($notes->id)[0];
                Attendant::where('note_id', $note_id)->delete();
                foreach( $request->attendants as $a){
                    Attendant::updateOrCreate([
                        'note_id'=> $note_id,
                        'user_id'=> Hashids::decode($a)[0]
                    ]);
                }
            }
            return redirect()->route("admin.notes.index")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(String $hashed_id)
    {
        $id = Hashids::decode($hashed_id);
        if(Note::findOrFail($id)->first()->delete()){
            return back()->with('success','Data <strong>berhasil</strong> dihapus!');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> dihapus!']);
        }
    }

    public function lock(String $hashed_id)
    {
        $id = Hashids::decode($hashed_id);
        $note = Note::findOrFail($id)->first();
        $status = $note->status == 'open'?'lock':'open';
        if($note->update(['status'=>$status])){
            return back()->with('success','Data <strong>berhasil</strong> diubah!');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> diubah!']);
        }
    }
}
