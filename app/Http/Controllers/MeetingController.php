<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendant;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class MeetingController extends Controller
{
    public function check_in(String $hashed_id)
    {
        $title = "Join Meeting";
        $id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $note = Note::find($id);
        if($note == NULL){
            return "404 - Meeting Not Found";
        }

        $users = User::all();
        return view('user.join_meeting', compact(['title','note','users']));
    }

    public function join_meeting(Request $request, String $hashed_id)
    {
        $id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $note = Note::find($id);
        if($note == NULL){
            return back()->withErrors(['Data <strong>tidak ditemukan</strong>!']);
        }
        // dd($request->nip);
        $nip = $request->nip;
        $user = User::firstWhere('nip',$nip);
        if($user == NULL){
            return back()->withErrors(['Data <strong>tidak ditemukan</strong>!']);
        }
        else{
            Attendant::updateOrCreate([
                'note_id' => Hashids::decode($note->id)[0],
                'user_id' => $user->id
            ]);
            Auth::login($user);
            return redirect()->route("home")->with('success','Data <strong>berhasil</strong> disimpan');
        }
    }
}
