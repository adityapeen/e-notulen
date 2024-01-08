<?php

namespace App\Http\Controllers\Admin;

use App\Models\Note;
use App\Models\Agenda;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GDocsController;
use App\Models\ActionItems;
use App\Models\Attendant;
use App\Models\Comment;
use App\Models\Evidence;
use App\Models\MomRecipients;
use App\Models\MSatker;
use App\Models\Pic;
use App\Models\User;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\DataTables\Facades\DataTables;

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
        $notes = Note::withCount(['action_items'])->orderBy('date', 'DESC')->paginate(15);
        $satkers = MSatker::all();
        return view('admin.note.index', compact(['notes','title','satkers']));
    }

    public function bySatker(Request $request, String $hashed_id){

        $title = "Daftar Notulensi";
        $satkers = MSatker::all();
        if($hashed_id == 'ALL'){
            return redirect()->route("admin.notes.index");
        }
        else if($hashed_id == 'BPS'){
            $notes = Note::withCount(['action_items'])->where('team_id',NULL)->orderBy('date','DESC');
        }
        else {
            $satker_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
            $notes = Note::withCount(['action_items'])->whereHas('team', function ($query) use($satker_id) {
                $query->where('satker_id', $satker_id);
            })->orderBy('date','DESC');
        }
        
        if ($request->ajax()) {
            return DataTables::of($notes)
            ->addColumn('name_b', function($row){
                $type = $row->type == 'public' ? 'success' : 'info';
                $name = '<h6 class="mb-0">'. $row->name .'</h6>
                <span class="badge badge-sm bg-gradient-'. $type .' px-1">&nbsp;</span>
                '.($row->team != null? '<span class="badge badge-sm bg-gradient-light text-dark">'. $row->team->satker->code .'</span>':'')
                 .($row->agenda != null ? '<span class="badge badge-sm bg-gradient-secondary">'. $row->agenda->name .'</span>' : '');
                return $name;
            })
            ->addColumn('action_item', function($row){
                $actionItem = '<a href="'.route('admin.notes.action', [$row->id]).'" class="btn btn-sm bg-gradient-info mb-0">Action
                Items <span class="badge bg-gradient-light text-dark ms-2">'.$row->action_items_count.'</span></a>';
                return $actionItem;
            })
            ->addColumn('status_b', function($row){
                $type = $row->status == 'open' ? 'success' : 'danger';
                $status = '<span class="badge badge-sm bg-gradient-'. $type .' btn mb-0"
                onclick="handleView(`admin`,`'. $row->id .'`)" data-toggle="tooltip"
                title="Lihat Notulensi">'. $row->status .'<div class="fa fa-eye"></div></span>';
                return $status;
            })
            ->addColumn('action', function($row){
                $bt_edit = '<a href="'. route('admin.notes.edit', [$row->id]) .'"
                class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Edit Notulensi">
                <button class="btn btn-sm btn-success mb-0"><i class="fa fa-edit"></i></button>
              </a>';
                $bt_docs = '<a href="'. route('api.gdocs', [$row->id]) .'" class="text-secondary font-weight-bold text-xs"
              data-toggle="tooltip" title="Generate File Notulen">
              <button class="btn btn-sm btn-secondary mb-0"><i class="fab fa-google-drive"></i></button>
            </a>';
                $bt_qr = '<a href="'. route('admin.notes.qrcode', [$row->id]) .'" target="_blank"
                class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="QR Join Meeting">
                <button class="btn btn-sm btn-dark mb-0"><i class="fa fa-qrcode"></i></button>
              </a>';
              $type = $row->status == 'lock' ? 'primary' : 'warning';
              $bt_lock = '<a href="#" onclick="handleLock(`admin`,`'. $row->id .'`)"
              class="text-secondary font-weight-bold text-xs" data-toggle="tooltip"
              title="'. ($row->status == 'lock' ? 'Buka' : 'Kunci' ).' Notulensi">
              <button class="btn btn-sm btn-'. $type .' mb-0"><i
                  class="fa fa-lock"></i></button>
            </a>';
                $bt_pdf = '<a href="'. route('admin.export.docs', [$row->id]) .'" target="_blank" class="text-secondary font-weight-bold text-xs"
                         data-toggle="tooltip" title="Generate PDF">
                         <button class="btn btn-sm btn-danger mb-0"><i class="fa fa-file-pdf"></i></button>
                      </a>';
                $bt_send = '<a href="#" onclick="handleSend(`admin`,`'. $row->id .'`)"
                class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Kirim MoM">
                <button class="btn btn-sm btn-info mb-0"><i class="fa fa-file"></i></button>
              </a>';
                $bt_absen = '<a href="'. route('admin.notes.absensi', $row->id).'" target="_blank"
                class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Daftar Hadir">
                <button class="btn btn-sm btn-warning mb-0"><i class="fa fa-list"></i></button>
              </a>';
                $bt_delete = '<a href="#" onclick="handleDestroy(`admin`,`'. $row->id .'`)"
                class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" title="Hapus Agenda">
                <button class="btn btn-sm btn-danger mb-0"><i class="fa fa-trash"></i></button>
              </a>';
                $actionBtn = $row->status != 'lock'? $bt_edit.($row->link_drive_notulen == '-'? $bt_docs : '').$bt_qr.$bt_lock.$bt_delete
                            :
                            $bt_lock.($row->file_notulen == NULL? $bt_pdf:'').$bt_send.$bt_absen;

                return $actionBtn;
            })
            ->rawColumns(['name_b','action','status_b','action_item'])->toJson();
        }
        
        return view('admin.note.index-table', compact(['title','satkers']));
    }

    public function bySatkerx(String $hashed_id)
    {
        if($hashed_id == 'ALL'){
            return redirect()->route("admin.notes.index");
        }
        else if($hashed_id == 'BPS'){
            $notes = Note::withCount(['action_items'])->where('team_id',NULL)->orderBy('date','DESC')->paginate(15);
        }
        else {
            $satker_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
            $notes = Note::withCount(['action_items'])->whereHas('team', function ($query) use($satker_id) {
                $query->where('satker_id', $satker_id);
            })->orderBy('date','DESC')->paginate(15);
        }
        $title = "Daftar Notulensi";
        $satkers = MSatker::all();
        return view('admin.note.index', compact(['notes','title','satkers']));
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
            if(valid_docs_id($note->link_drive_notulen)){
                $file_id = valid_docs_id($note->link_drive_notulen);
                $GDocs = new GDocsController();
                $GDocs->changeFilePremission( $file_id,$note->status);
            }
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
            return view('admin.action.view', compact(['title','note','actions', 'attendants']));
        if(sizeof($actions)>0)
            return view('admin.action.edit', compact(['title','note','actions', 'attendants']));
        else 
            return view('admin.action.create', compact(['title','note','actions', 'attendants']));
    }

    public function byAgenda(String $hashed_id){
        $agenda_id = Hashids::decode($hashed_id)[0];
        $agenda = Agenda::findOrFail($agenda_id);
        $title = "Daftar Notulensi - ".$agenda->name;
        $pending_actions = ActionItems::whereHas('note', function ($query) use ($agenda_id) {
            $query->where('agenda_id', $agenda_id);
        })->whereNot('status','done')->get();
        $notes = Note::withCount(['action_items'])->where('agenda_id', $agenda_id)->orderBy('date', 'DESC')->paginate(15);

        return view('admin.note.index-agenda', compact(['title','notes','agenda_id','agenda','pending_actions']));
    }

    public function showNote(String $hashed_id){
        $title = "Notulensi";
        $note_id = Hashids::decode($hashed_id)[0];
        $note = Note::findOrFail($note_id);
        return view('admin.note.view', compact(['title','note']));
    }

    public function evidence(String $hashed_id){
        $title = "Eviden Action Item";
        $action_id = Hashids::decode($hashed_id)[0];
        // dd($action_id);
        $action = ActionItems::findOrFail($action_id);
        $evidences = Evidence::where('action_id', $action_id)->get();
        $pics = Pic::where('action_id', $action_id)->get();
        $comments = Comment::where('action_id', $action_id)->count();
        return view('admin.evidence.index', compact(['title','action','evidences','pics','comments']));
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
        ->where('satker_id', NULL)
        ->get();
        $color = ['primary','dark','info','warning','success','light'];
        // dd($agendas);
        return view('admin.note.note_group', compact(['agendas','title','color']));
    }
}
