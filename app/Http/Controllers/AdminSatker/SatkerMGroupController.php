<?php

namespace App\Http\Controllers\AdminSatker;

use App\Models\MGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class SatkerMGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Group Rapat";
        $groups = MGroup::where('satker_id', auth()->user()->satker_id )->get();
        return view('satker.group.index', compact(['groups','title']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Tambah Group";
        return view('satker.group.create', compact('title'));
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
            'name' => ['required']
        ]);
        if(MGroup::updateOrCreate([
            'name' => $request->name,
            'satker_id' => auth()->user()->satker_id,
        ])){
            return redirect()->route("satker.groups.index")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\M_Group  $m_Group
     * @return \Illuminate\Http\Response
     */
    public function show(MGroup $m_Group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\M_Group  $m_Group
     * @return \Illuminate\Http\Response
     */
    public function edit(String $hashed_id)
    {
        $title = "Edit Group";
        $id = Hashids::decode($hashed_id); //decode the hashed id
        $group = MGroup::find($id[0]);

        return view('satker.group.edit', compact('group','title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\M_Group  $m_Group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, String $hashed_id)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $id = Hashids::decode($hashed_id);
        if(MGroup::findOrFail($id)->first()->update($request->all())){
            return redirect()->route("satker.groups.index")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\M_Group  $m_Group
     * @return \Illuminate\Http\Response
     */
    public function destroy(String $hashed_id)
    {
        $id = Hashids::decode($hashed_id);
        if(MGroup::findOrFail($id)->first()->delete()){
            return back()->with('success','Data <strong>berhasil</strong> dihapus!');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> dihapus!']);
        }
    }
}
