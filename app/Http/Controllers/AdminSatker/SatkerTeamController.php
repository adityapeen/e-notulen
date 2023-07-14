<?php

namespace App\Http\Controllers\AdminSatker;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class SatkerTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Bidang / Kelompok Kerja";
        $teams = Team::where('satker_id', auth()->user()->satker_id)->get();
        return view('satker.team.index', compact(['teams','title']));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Tambah Bidang";
        return view('satker.team.create', compact('title'));
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
            'code' => ['required'],
        ]);
        if(Team::updateOrCreate([
            'code' => $request->code,
            'name' => $request->name,
            'satker_id' => auth()->user()->satker_id,
            'created_by' => auth()->user()->id,
        ])){
            return redirect()->route("satker.teams.index")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(String $hashed_id)
    {
        $title = "Edit Bidang";
        $id = Hashids::decode($hashed_id); //decode the hashed id
        $team = Team::find($id[0]);

        return view('satker.team.edit', compact('team','title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, String $hashed_id)
    {
        $request->validate([
            'name' => ['required'],
            'code' => ['required'],
        ]);
        $id = Hashids::decode($hashed_id)[0];
        if(Team::findOrFail($id)->update([
            'code' => $request->code,
            'name' => $request->name,
            'updated_by' => auth()->user()->id,
        ])){
            return redirect()->route("satker.teams.index")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(String $hashed_id)
    {
        $id = Hashids::decode($hashed_id);
        if(Team::findOrFail($id)->first()->delete()){
            return back()->with('success','Data <strong>berhasil</strong> dihapus!');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> dihapus!']);
        }
    }
}
