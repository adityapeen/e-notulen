<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function markNotification(Request $request)
    {
        auth()->user()
            ->unreadNotifications
            ->when($request->input('id'), function ($query) use ($request) {
                return $query->where('id', $request->input('id'));
            })
            ->markAsRead();

        if($request->id != NULL){            
            $action_id = DatabaseNotification::find($request->id)->data['action_id'];
            if(auth()->user()->current_role_id == 9){
                $url = url('user/notes/action/'.$action_id.'/evidences');
            }
            else if(auth()->user()->current_role_id == 7 || auth()->user()->current_role_id == 8){
                $url = url('satker/notes/action/'.$action_id.'/evidences');
            }
            else $url = url('admin/notes/action/'.$action_id.'/evidences');
        }
        else $url = url('home');
        return response()->json(['url' => $url]);
    }
}
