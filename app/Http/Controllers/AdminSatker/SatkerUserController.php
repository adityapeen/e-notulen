<?php

namespace App\Http\Controllers\AdminSatker;

use App\Models\User;
use App\Models\MLevel;
use App\Models\MSatker;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Vinkla\Hashids\Facades\Hashids;

class SatkerUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Daftar User";
        $users = User::where('satker_id', auth()->user()->satker_id)
                        ->where('level_id', '>=', auth()->user()->level_id)->get();
        return view('satker.user.index', compact(['users','title']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 
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
    public function edit($hashed_id)
    {
        $title = "Edit User";
        $id = Hashids::decode($hashed_id); //decode the hashed id
        $user = User::find($id[0]);
        $levels = MLevel::where('id','>=',auth()->user()->level_id)->get();
        $teams = Team::where('satker_id', auth()->user()->satker_id)->get();
        return view('satker.user.edit', compact('title','user','levels','teams'));
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
            'phone' => ['required'],
            'level_id' => ['required'],
        ]);
        $id = Hashids::decode($hashed_id);
        $team_id = Hashids::decode($request->team_id)[0];
        if(User::findOrFail($id)->first()->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'phone' => $request->phone,
            'level_id' => $request->level_id,
            'team_id' => $team_id,
        ])){
            return redirect()->route("satker.users.index")->with('success','Data <strong>berhasil</strong> disimpan');
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
    public function destroy($hashed_id)
    {
        $id = Hashids::decode($hashed_id);
        if(User::findOrFail($id)->first()->delete()){
            return back()->with('success','Data <strong>berhasil</strong> dihapus!');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> dihapus!']);
        }
    }

    public function password()
    {
        $title = "Change Password";
        $user = User::findOrFail(auth()->user()->id);
        if($user->level_id <= 2){
            return view('admin.user.password', compact(['user','title']));
        }
        else {
            return view('user.password', compact(['user','title']));
        }
    }

    public function change_password(Request $request){
        $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required'],
        ]);
        $user = User::findOrFail(auth()->user()->id);
 
        #Match The Old Password
        if(!Hash::check($request->old_password, auth()->user()->password)){
            return back()->withErrors(["<strong>Old Password doesn't match!</strong>"]);
        }

        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success','Password <strong>berhasil</strong> diubah!');
    }

    public function profile(){
        $title = "Profile";
        $user = User::findOrFail(auth()->user()->id);
        $satkers = MSatker::all();
        return view('user.profile', compact(['user','title','satkers']));
    }

    public function self_update(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
            'satker_id' => ['required'],
        ]);

        if(User::findOrFail(auth()->user()->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'satker_id' => $request->satker_id,
            'status' => 1,
        ])){
            return redirect()->route("user.profile")->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }
}
