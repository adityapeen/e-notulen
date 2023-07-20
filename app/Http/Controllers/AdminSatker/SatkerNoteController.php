<?php

namespace App\Http\Controllers\AdminSatker;

use App\Models\Note;
use App\Models\Agenda;
use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\Attendant;
use App\Models\Evidence;
use App\Models\MomRecipients;
use App\Models\Pic;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class SatkerNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Daftar Notulensi";
        if(auth()->user()->level_id == 8){ // Admin Bidang
            $notes = Note::orderBy('date', 'DESC')->where('team_id', auth()->user()->team_id)->paginate(15);
        }
        else { // Admin Satker
            $notes = Note::with('team')->orderBy('date', 'DESC')
            ->whereHas('team', function ($query) {
                $query->where('satker_id', auth()->user()->satker_id);
            })->paginate(15);
        }
        return view('satker.note.index', compact(['notes','title']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Buat Notulensi";
        $agendas = Agenda::where('satker_id', auth()->user()->satker_id)->get();
        $users = User::all();
        $types = (object) [(object)['id'=>'public', 'name'=>'Publik'],(object)['id'=>'internal','name'=>'Internal']];
        return view('satker.note.create', compact(['agendas','types','title','users']));
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
            $base_name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $nama_file =$base_name.'_'.time().'.'.$file->getClientOriginalExtension(); // add timestamp to filename
                           
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
                'place' => $request->place,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'max_execute' => $request->max_execute,
                'issues' => $request->issues,
                'link_drive_notulen' => '-',
                'file_notulen' => $nama_file,
                'status' => 'open',
                'created_by' => auth()->user()->id,
                'team_id' => auth()->user()->team_id
        ]);
        if($notes){
            if($request->attendants != null){
                foreach( $request->attendants as $a){
                    Attendant::updateOrCreate([
                        'note_id'=>Hashids::decode($notes->id)[0],
                        'user_id'=> Hashids::decode($a)[0]
                    ]);
                }
            }
            if($request->mom_recipients != null){
                foreach( $request->mom_recipients as $r){
                    MomRecipients::updateOrCreate([
                        'note_id'=>Hashids::decode($notes->id)[0],
                        'user_id'=> Hashids::decode($r)[0]
                    ]);
                }
            }
            return redirect()->route("satker.notes.index")->with('success','Data <strong>berhasil</strong> disimpan');
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
        $list_r = MomRecipients::where(['note_id'=>$id])->get();
        $attendants = array();
        $recipients = array();
        foreach($list as $l){
            $date = new DateTime($l->mom_sent, new DateTimeZone('GMT'));
            $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
            $item = array(
                'name' => $l->user->name,
                'mom_sent' => $l->mom_sent == NULL? NULL : $date->format('Y-m-d H:i:s')
            );
            array_push($attendants,$item);
        }
        foreach($list_r as $l){
            $date = new DateTime($l->mom_sent, new DateTimeZone('GMT'));
            $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
            $item = array(
                'name' => $l->user->name,
                'mom_sent' => $l->mom_sent == NULL? NULL : $date->format('Y-m-d H:i:s')
            );
            array_push($recipients,$item);
        }
        return compact(['note','attendants','recipients']);
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

        return view('satker.note.edit', compact('title','types','agendas','note','users'));
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
            $base_name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $nama_file =$base_name.'_'.time().'.'.$file->getClientOriginalExtension(); // add timestamp to filename
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
            'place' => $request->place,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_execute' => $request->max_execute,
            'issues' => $request->issues,
            'file_notulen' => $nama_file,
            'status' => 'open',
            'updated_by' => auth()->user()->id,
        ]) ;
        if($notes){
            if($request->attendants != null){
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
            if($request->mom_recipients != null){
                $note_id = Hashids::decode($notes->id)[0];
                $recipients = MomRecipients::where('note_id', $note_id)->get();

                $existing = array();
                foreach ($recipients as $r){
                    $data = $r->user_id;
                    array_push($existing, $data);
                }
                
                $new = array();
                foreach( $request->mom_recipients as $r){
                    $data =  Hashids::decode($r)[0];
                    array_push($new, $data);
                }

                $to_insert = array_diff($new,$existing);
                $to_delete = array_diff($existing, $new);

                foreach($to_delete as $user_id){
                    MomRecipients::where(['note_id'=> $note_id, 'user_id' => $user_id])->first()->delete();
                }
                foreach($to_insert as $user_id){
                    MomRecipients::updateOrCreate([
                        'note_id'=>$note_id,
                        'user_id'=> $user_id
                    ]);
                }
            }
            return redirect()->route("satker.notes.index")->with('success','Data <strong>berhasil</strong> disimpan');
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
        $actions = ActionItems::withCount(['evidences'])->where('note_id',$note_id)->get();
        $attendants = Attendant::where('note_id',$note_id)->get();
        if($note->status=='lock')
            return view('satker.note.action_view', compact(['title','note','actions', 'attendants']));
        if(sizeof($actions)>0)
            return view('satker.note.action_edit', compact(['title','note','actions', 'attendants']));
        else 
            return view('satker.note.action', compact(['title','note','actions', 'attendants']));
    }

    public function byAgenda(String $hashed_id){
        $agenda_id = Hashids::decode($hashed_id)[0];
        $agenda = Agenda::findOrFail($agenda_id);
        $title = "Daftar Notulensi - ".$agenda->name;
        $notes = Note::where('agenda_id', $agenda_id)->orderBy('date', 'DESC')->paginate(15);

        return view('satker.note.index-agenda', compact(['title','notes','agenda_id','agenda']));
    }

    public function showNote(String $hashed_id){
        $title = "Notulensi";
        $note_id = Hashids::decode($hashed_id)[0];
        $note = Note::findOrFail($note_id);
        return view('satker.note.view', compact(['title','note']));
    }

    public function evidence(String $hashed_id){
        $title = "Eviden Action Item";
        $action_id = Hashids::decode($hashed_id)[0];
        // dd($action_id);
        $action = ActionItems::findOrFail($action_id);
        $evidences = Evidence::where('action_id', $action_id)->get();
        $pics = Pic::where('action_id', $action_id)->get();
        return view('admin.evidence.index', compact(['title','action','evidences','pics']));
    }

    public function qrcode(String $hashed_id){
        $title = "QR Checkin";
        $note_id = Hashids::decode($hashed_id)[0];
        $note = Note::find($note_id);
        return view('admin.note.qrcode', compact(['title','note']));
    }

    public function groupByAgenda(){
        $title = "Daftar Rapat";
        $agendas = Agenda::
        select('agendas.*', DB::raw('(SELECT MAX(date) FROM notes WHERE notes.agenda_id = agendas.id) AS last_note_date'),
        DB::raw('(SELECT COUNT(*) FROM notes WHERE notes.agenda_id = agendas.id) as notes_count'))
        ->orderBy('priority_id', 'asc')
        ->orderBy('agendas.name', 'asc')
        ->get();
        $color = ['primary','dark','info','warning','success','light'];
        // dd($agendas);
        return view('satker.note.note_group', compact(['agendas','title','color']));
    }
}
