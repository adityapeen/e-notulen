<?php

namespace App\Http\Controllers\Admin;

use App\Models\Note;
use App\Models\Agenda;
use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\Attendant;
use App\Models\User;
use App\Models\UserGroup;
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
            // 'agenda_id' => ['required'],
            'type' => ['required'],
            'name' => ['required'],
            'date' => ['required'],
            'file_notulen' => ['max:10000']
        ]);
     
        if ($request->file('file_notulen') != NULL) {            
            $file = $request->file('file_notulen'); // menyimpan data file yang diupload ke variabel $file
            $nama_file = $file->getClientOriginalName().'_'.time(); // add timestamp to filename
                           
            $tujuan_upload = 'notulensi'; // isi dengan nama folder tempat kemana file diupload
            $file->move($tujuan_upload,$nama_file);
        }
        else {
            $nama_file = NULL;
        }
     
        $notes = Note::updateOrCreate([
                'agenda_id' => $request->agenda_id == NULL ? NULL : Hashids::decode($request->agenda_id)[0],
                'type' => $request->type,
                'name' => $request->name,
                'date' => $request->date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'max_execute' => $request->max_execute,
                'issues' => $request->issues,
                'link_drive_notulen' => $request->link_drive_notulen,
                'file_notulen' => $nama_file,
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
    public function show(String $hashed_id)
    {
        $id = Hashids::decode($hashed_id); //decode the hashed id
        $note = Note::find($id[0]);
        $list = Attendant::where(['note_id'=>$id])->get();
        $attendants = array();
        foreach($list as $l){
            array_push($attendants,$l->user->name);
        }
        return compact(['note','attendants']);
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
            // 'agenda_id' => ['required'],
            'type' => ['required'],
            'name' => ['required'],
            'date' => ['required'],
            'file_notulen' => ['max:10000']
        ]);
        $id = Hashids::decode($hashed_id);
        $notes = Note::findOrFail($id)->first();

        if ($request->hasFile('file_notulen')) {
            $directory = 'notulensi'; // isi dengan nama folder tempat kemana file diupload
            if($notes->file_notulen != NULL){
                try {
                    $file_path = realpath($directory . '/' . $notes->file_notulen);
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                } catch (Throwable $e) {
                    $e;
                }         
            }
            $file = $request->file('file_notulen'); // menyimpan data file yang diupload ke variabel $file
            $nama_file = time().'_'.$file->getClientOriginalName();
            $file->move($directory,$nama_file);      
        }
        else {
            $nama_file = $notes->file_notulen;
        }

        $notes->update([
            'agenda_id' => $request->agenda_id == NULL ? NULL : Hashids::decode($request->agenda_id)[0],
            'type' => $request->type,
            'name' => $request->name,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_execute' => $request->max_execute,
            'issues' => $request->issues,
            'link_drive_notulen' => $request->link_drive_notulen,
            'file_notulen' => $nama_file,
            'status' => 'open',
            'updated_by' => auth()->user()->id,
        ]) ;
        if($notes){
            if(sizeof($request->attendants)>0){
                $note_id = Hashids::decode($notes->id)[0];
                $attendants = Attendant::where('note_id', $note_id)->get();

                $existing = array();
                foreach ($attendants as $a){
                    $data = $a->user_id;
                    array_push($existing, $data);
                }
                
                $new = array();
                foreach( $request->attendants as $a){
                    $data =  Hashids::decode($a)[0];
                    array_push($new, $data);
                }

                $to_insert = array_diff($new,$existing);
                $to_delete = array_diff($existing, $new);

                // dd($to_delete, $to_insert);

                foreach($to_delete as $user_id){
                    Attendant::where(['note_id'=> $note_id, 'user_id' => $user_id])->first()->delete();
                }
                foreach($to_insert as $user_id){
                    Attendant::updateOrCreate([
                        'note_id'=>$note_id,
                        'user_id'=> $user_id
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

    public function action_item(String $hashed_id){
        $title = "Action Items";
        $note_id = Hashids::decode($hashed_id)[0];
        $note = Note::findOrFail($note_id);
        $actions = ActionItems::where('note_id',$note_id)->get();
        $attendants = UserGroup::where('agenda_id',$note->agenda_id)->get();
        if(sizeof($actions)>0)
            return view('admin.note.action_edit', compact(['title','note','actions', 'attendants']));
        else 
            return view('admin.note.action', compact(['title','note','actions', 'attendants']));
    }
}
