<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Attendant;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\MSatker;
use App\Models\Note;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Vinkla\Hashids\Facades\Hashids;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if(array_key_exists('meeting',$data)){
            Session::flash('meeting', $data['meeting']);
        }
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'satker_id' => ['required', 'string'],
            'phone' => ['required', 'string', 'min:10'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'satker_id' => $data['satker_id'],
            'level_id' => 9,
            'current_role_id' => 9,
            'phone' => $data['phone']
        ]);
        if(array_key_exists('meeting',$data)){
            $this->addUserToMeeting($data, $user->id);
        }
        else {
            echo 'normal';
        }
        return $user;
    }

    public function showRegistrationForm()
    {
        $satkers = MSatker::get();

        return view('auth.register', compact("satkers"));
    }

    protected function addUserToMeeting($data, $user_id)
    {
        $id = Hashids::decode($data['meeting'])[0]; //decode the hashed id
        $note = Note::find($id);
        if($note == NULL){
            return back()->withErrors(['Data <strong>tidak ditemukan</strong>!']);
        }
        $user = User::findOrFail($user_id);
        if($user == NULL){
            return back()->withErrors(['Data <strong>tidak ditemukan</strong>!']);
        }
        else{
            Attendant::updateOrCreate([
                'note_id' => Hashids::decode($note->id)[0],
                'user_id' => $user->id
            ]);
        }
    }

}
