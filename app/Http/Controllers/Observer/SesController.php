<?php

namespace App\Http\Controllers\Observer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

use App\Models\ActionItems;
use App\Models\Agenda;
use App\Models\Attendant;
use App\Models\Evidence;
use App\Models\MomRecipients;
use App\Models\MSatker;
use App\Models\Note;
use App\Models\Pic;
use App\Models\User;
use DateTime;
use DateTimeZone;

class SesController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
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
                            })->paginate(10);
        $todays = Note::where('date', date('Y-m-d'))->get();
        return view('observer.ses.notulen', compact(['users','agendas','notes','actions','todays','title','wa_ready','notes_locked','actions_todo','actions_progress','undone','notes_satkers']));
    }

    public function agenda()
    {
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
        return view('observer.ses.note_group', compact(['agendas','title','color']));
    }

    public function notes(String $hashed_id)
    {
        if($hashed_id == 'ALL'){
            $notes = Note::withCount(['action_items'])->orderBy('date','DESC')->paginate(15);
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
        return view('observer.ses.note_index', compact(['notes','title','satkers']));
    }

    public function byAgenda(String $hashed_id){
        $agenda_id = Hashids::decode($hashed_id)[0];
        $agenda = Agenda::findOrFail($agenda_id);
        $title = "Daftar Notulensi - ".$agenda->name;
        $notes = Note::where('agenda_id', $agenda_id)->orderBy('date', 'DESC')->paginate(15);

        return view('observer.ses.note_agenda', compact(['title','notes','agenda_id','agenda']));
    }

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

    public function showNote(String $hashed_id){
        $title = "Notulensi";
        $note_id = Hashids::decode($hashed_id)[0];
        $note = Note::findOrFail($note_id);
        return view('observer.ses.view_note', compact(['title','note']));
    }

    public function action_item(String $hashed_id){
        $title = "Action Items";
        $note_id = Hashids::decode($hashed_id)[0];
        $note = Note::findOrFail($note_id);
        $actions = ActionItems::withCount(['evidences'])->where('note_id',$note_id)->get();
        $attendants = Attendant::where('note_id',$note_id)->get();
        return view('observer.ses.action', compact(['title','note','actions', 'attendants']));
    }

    public function evidence(String $hashed_id){
        $title = "Eviden Action Item";
        $action_id = Hashids::decode($hashed_id)[0];
        $action = ActionItems::findOrFail($action_id);
        $evidences = Evidence::where('action_id', $action_id)->get();
        $pics = Pic::where('action_id', $action_id)->get();
        return view('observer.ses.evidence', compact(['title','action','evidences','pics']));
    }
}
