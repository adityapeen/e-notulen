<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MLevel;
use Illuminate\Http\Request;

class MLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Daftar Level User";
        $levels = MLevel::all();
        return view('admin.user-level.index', compact(['levels','title']));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //  Your Code
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Your Code
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function show(MLevel $level)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function edit(String $id)
    {
        $title = "Edit Level User";
        $level = MLevel::find($id);

        return view('admin.user-level.edit', compact('title','level'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $agenda = MLevel::findOrFail($id)->update([
            'name' => $request->name,
        ]);
        if($agenda){
            return redirect()->route("admin.levels.index")->with('success','Data <strong>berhasil</strong> disimpan');
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
        // Your Code
    }

}
