<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agenda;
use App\Http\Controllers\Controller;
use App\Models\MGroup;
use App\Models\User;
use App\Models\UserGroup;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Daftar Rapat";
        $agendas = Agenda::all();
        return view('admin.agenda.index', compact(['agendas','title']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Tambah Agenda Rapat";
        $groups = MGroup::all();
        $users = User::all();
        return view('admin.agenda.create', compact(['title','groups','users']));
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
            'name' => ['required'],
            // 'group_id' => ['required'],
        ]);
        $agenda = Agenda::updateOrCreate([
            'name' => $request->name,
            'group_id' => $request->group_id == NULL ? NULL : Hashids::decode($request->group_id)[0],
            'created_by' => auth()->user()->id,
        ]);
        if($agenda){
            if($request->attendants != null){
                foreach( $request->attendants as $a){
                    UserGroup::updateOrCreate([
                        'agenda_id'=>Hashids::decode($agenda->id)[0],
                        'user_id'=> Hashids::decode($a)[0]
                    ]);
                }
            }
            return redirect()->route("admin.agendas.index")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function show(Agenda $agenda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function edit(String $hashed_id)
    {
        $title = "Edit User";
        $id = Hashids::decode($hashed_id); //decode the hashed id
        $agenda = Agenda::find($id[0]);
        $groups = MGroup::all();
        $users = User::all();

        return view('admin.agenda.edit', compact('title','agenda','groups','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $hashed_id)
    {
        $request->validate([
            'name' => ['required'],
            // 'group_id' => ['required'],
        ]);
        $id = Hashids::decode($hashed_id)[0];
        $agenda = Agenda::findOrFail($id)->first()->update([
            'name' => $request->name,
            'group_id' => $request->group_id == NULL ? NULL : Hashids::decode($request->group_id)[0],
            'updated_by' => auth()->user()->id,
        ]);
        if($agenda){
            if($request->attendants != null){
                $agenda_id = $id;
                $user_groups = UserGroup::where('agenda_id', $agenda_id)->get();
                $existing = array();

                foreach ($user_groups as $a){
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
                    UserGroup::where(['agenda_id'=> $agenda_id, 'user_id' => $user_id])->first()->delete();
                }
                foreach($to_insert as $user_id){
                    UserGroup::updateOrCreate([
                        'agenda_id'=>$agenda_id,
                        'user_id'=> $user_id
                    ]);
                }
            }
            return redirect()->route("admin.agendas.index")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function destroy(String $hashed_id)
    {
        $id = Hashids::decode($hashed_id);
        if(Agenda::findOrFail($id)->first()->delete()){
            return back()->with('success','Data <strong>berhasil</strong> dihapus!');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> dihapus!']);
        }
    }
}
