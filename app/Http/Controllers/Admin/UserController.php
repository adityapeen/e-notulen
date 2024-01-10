<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\MLevel;
use App\Models\MSatker;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Vinkla\Hashids\Facades\Hashids;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Daftar User";
        $users = User::all();
        return view('admin.user.index', compact(['users','title']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Tambah User";
        $satkers = MSatker::all();
        $levels = MLevel::all();
        return view('admin.user.create', compact(['title','satkers','levels']));
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
            'nip' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required'],
            'satker_id' => ['required'],
            // 'level_id' => ['required'],
        ]);
        if(User::updateOrCreate([
            'name' => $request->name,
            'password' => Hash::make('12345'),
            'nip' => $request->nip,
            'email' => $request->email,
            'phone' => $request->phone,
            'satker_id' => $request->satker_id,
            'current_level_id' => 9,
            'status' => 1,
        ])){
            return redirect()->route("admin.users.index")->with('success','Data <strong>berhasil</strong> disimpan');
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
    public function edit($hashed_id)
    {
        $title = "Edit User";
        $id = Hashids::decode($hashed_id); //decode the hashed id
        $user = User::find($id[0]);
        $satkers = MSatker::all();
        $levels = MLevel::all();
        $teams = Team::all();
        $roles = Role::all();
        $assigned_roles =  $user->roles->pluck('id');

        return view('admin.user.edit', compact('title','user','satkers','levels','teams','roles','assigned_roles'));
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
            'satker_id' => ['required'],
            // 'level_id' => ['required'],
        ]);
        $id = Hashids::decode($hashed_id);
        $user = User::findOrFail($id[0]);
        if($user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'phone' => $request->phone,
            'satker_id' => $request->satker_id,
            // 'level_id' => $request->level_id,
            'current_role_id' => $request->roles[0],
            'team_id' => Hashids::decode($request->team_id)[0],
        ])){
            $new_role = array();
            foreach ($request->roles as $role){
                $r = Role::findById($role);
                array_push($new_role, $r->name);
            }
            $user->syncRoles($new_role);
            return redirect()->route("admin.users.index")->with('success','Data <strong>berhasil</strong> disimpan');
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
        else if($user->level_id <= 8){
            return view('satker.user.password', compact(['user','title']));
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
