<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agenda;
use App\Http\Controllers\Controller;
use App\Models\MGroup;
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
        return view('admin.agenda.create', compact(['groups','title']));
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
            'group_id' => ['required'],
        ]);
        if(Agenda::updateOrCreate([
            'name' => $request->name,
            'group_id' => Hashids::decode($request->group_id)[0],
            'created_by' => auth()->user()->id,
        ])){
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

        return view('admin.agenda.edit', compact('title','agenda','groups'));
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
            'group_id' => ['required'],
        ]);
        $id = Hashids::decode($hashed_id);
        if(Agenda::findOrFail($id)->first()->update([
            'name' => $request->name,
            'group_id' => Hashids::decode($request->group_id)[0],
            'updated_by' => auth()->user()->id,
        ])){
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
