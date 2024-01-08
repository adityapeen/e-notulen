<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Controllers\Controller;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class CommentController extends Controller
{
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
              <div class="text-start text-capitalize mb-1 fw-light">'.$name.'</div>
              <div class="fw-light text-capitalize text-wrap text-start">'.
              $comment->message
              .'</div>
              <span class="float-end mt-1 fw-light">'.$time.'</span>
            </div>
          </div>';
        }
        return $item;
    }
}
