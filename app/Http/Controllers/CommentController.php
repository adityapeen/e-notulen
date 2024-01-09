<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Vinkla\Hashids\Facades\Hashids;

class CommentController extends Controller
{
    protected $WAController;

    public function __construct(WAController $WAController)
    {
        $this->WAController = $WAController;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $request->validate([
            'action_id' => ['required'],
            'message' => ['required'],
        ]);
        $action_id = Hashids::decode($request->action_id)[0]; //decode the hashed id
        if(Comment::updateOrCreate([
            'action_id' => $action_id,
            'message' => $request->message,
            'user_id' => auth()->user()->id,
        ])){
            $action_item = ActionItems::find($action_id);
            $this->_notify_user($action_item);
            return response('OK');
        }else{
            return response('Error', 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }

    public function get_comments(String $hashed_id)
    {
        $action_id = Hashids::decode($hashed_id)[0]; //decode the hashed id
        $comments = Comment::where('action_id', $action_id)->get();
        $data = array();
        foreach($comments as $c){
            $item = $this->_generate_chat($c);
            array_push($data, $item);
        }
        return $data;
    }

    private function _generate_chat(Comment $comment)
    {
        $tz = 'Asia/Jakarta';
        $timestamp = strtotime($comment->created_at);
        $dt = new DateTime("now", new DateTimeZone($tz)); //first argument "must" be a string
        $dt->setTimestamp($timestamp); //adjust the object to correct timestamp
        $time = $dt->format('Y-m-d H:i:s');

        if(auth()->user()->id == $comment->user_id){
            $item = '<div class="d-flex mb-1 justify-content-end">
            <div class="badge px-2 py-1 bg-gradient-success max-width-300">
              <div class="fw-light text-capitalize text-wrap text-start">'.
                $comment->message
                .'</div>
                <span class="float-end mt-1 fw-light">'.$time.'</span>
            </div>
          </div>';
        }
        else{
            if($comment->user->current_role_id < 3 ) $name = "Ka. BPSDM / Superadmin";
            else if ($comment->user->current_role_id == 3) $name = "Ses. BPSDM";
            else $name = $comment->user->name;

            $item ='<div class="d-flex mb-1">
            <div class="badge px-2 bg-gradient-secondary max-width-300">
              <div class="text-start text-capitalize mb-1">'.$name.'</div>
              <div class="fw-light text-capitalize text-wrap text-start">'.
              $comment->message
              .'</div>
              <span class="float-end mt-1 fw-light">'.$time.'</span>
            </div>
          </div>';
        }
        return $item;
    }

    private function _notify_user(ActionItems $action_item){
        $action_id = Hashids::decode($action_item->id)[0];
        $roles = [2,3,4,7,8]; // Admin Roles
        if(auth()->user()->current_role_id < 3) // Superadmin
        {
            $name = "Ka. BPSDM / Superadmin";
            $targets = User::whereHas('pics', function ($query) use ($action_id) {
                            $query->where('action_id', $action_id);
                        })->get();
        }
        else if(auth()->user()->current_role_id == 3) // Ses
        {
            $name = "Ses. BPSDM";
            $targets = User::whereHas('pics', function ($query) use ($action_id) {
                $query->where('action_id', $action_id);
            })->get();
        }
        else if(auth()->user()->current_role_id == 4) // Kapus
        {
            $name = "Kepala Pusat";
            $targets = User::whereHas('pics', function ($query) use ($action_id) {
                $query->where('action_id', $action_id);
            })->get();
        }
        else if(auth()->user()->current_role_id == 7 || auth()->user()->current_role_id == 8) // Admin Satker & Bidang
        {
            $name = "Admin";
            $targets = User::whereHas('pics', function ($query) use ($action_id) {
                $query->where('action_id', $action_id);
            })->get();
        }
        else {
            $name = auth()->user()->name;
            $targets = User::whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('id', $roles);
            })->get();
        }

        if(in_array(auth()->user()->current_role_id, $roles)){
            $this->sendWANotif($targets, $name, $action_item);
        }
        Notification::send($targets, new NewCommentNotification($name, $action_item));
    }

    private function sendWANotif($targets, String $name, ActionItems $action_item){
        $data = [
            "message" => $name." telah memberikan komentar pada eviden action item *".$action_item->note->name
                            ."*.\n\nSilahkan cek pada link berikut :\n".route('user.notes.evidence', $action_item->id)
                            ."\n\nTerimakasih 🙏"
        ];
        foreach($targets as $u){
            $data['recipient'] = $u->id;
            $this->WAController->send_message( (object) $data);
        }
    }
}
