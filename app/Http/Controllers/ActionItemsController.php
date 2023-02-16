<?php

namespace App\Http\Controllers;

use App\Models\ActionItems;
use App\Http\Controllers\Controller;
use App\Models\Pic;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class ActionItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return "iyey";
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
        $loop = sizeof($request->what);
        $note_id = Hashids::decode($request->note_id)[0];
        for ($i=0;$i<$loop;$i++){
            $act = ActionItems::updateOrCreate([
                'note_id' =>$note_id,
                'what' => $request->what[$i],
                'how' => $request->how[$i],
                'due_date' => $request->due_date[$i],
                'created_by' => auth()->user()->id,
            ]);
            if(!$act){
                return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
            }
            foreach($request->who[$i] as $pic){
                Pic::updateOrCreate([
                    'action_id' => Hashids::decode($act->id)[0],
                    'user_id' => Hashids::decode($pic)[0]
                ]);
            }
        }
        return redirect()->route("admin.notes.action", $request->note_id)->with('success','Data <strong>berhasil</strong> disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ActionItems  $actionItems
     * @return \Illuminate\Http\Response
     */
    public function show(ActionItems $actionItems)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ActionItems  $actionItems
     * @return \Illuminate\Http\Response
     */
    public function edit(ActionItems $actionItems)
    {
        return "iyey";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ActionItems  $actionItems
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, String $hashed_id)
    {
        $note_id = Hashids::decode($hashed_id)[0];
        $edited_item = array_filter($request->action_id); //remove null element
        $existing_action = ActionItems::select('id')->where('note_id', $note_id)->get()->toArray();
        $existing_item = array();
        foreach($existing_action as $item){ array_push($existing_item, $item['id']);}

        $to_delete = array_diff($existing_item, $edited_item);
        $rows = sizeof($request->action_id);
        for($i=0; $i<$rows; $i++){
            if($request->action_id[$i] !== null){ // Update Existing Action Item
                $action_id = Hashids::decode($request->action_id[$i])[0];
                ActionItems::where('id',$action_id)
                ->update([
                    'what'=>$request->what[$i],
                    'how'=>$request->how[$i],
                    'due_date'=>$request->due_date[$i],
                ]);

                $pics = Pic::select('user_id')->where('action_id', $action_id)->get()->toArray();
                $existing_pic = array();
                foreach($pics as $item){ array_push($existing_pic, $item['user_id']);}
                
                $new_pic = array();
                foreach( $request->who[$i] as $a){array_push($new_pic, Hashids::decode($a)[0]);}

                $to_insert_pic = array_diff($new_pic,$existing_pic);
                $to_delete_pic = array_diff($existing_pic, $new_pic);

                foreach($to_delete_pic as $user_id){
                    Pic::where(['action_id'=> $action_id, 'user_id' => $user_id])->first()->delete();
                }
                foreach($to_insert_pic as $user_id){
                     Pic::updateOrCreate([
                        'action_id' => $action_id,
                        'user_id' => $user_id,
                    ]);
                }
            } 
            else { // Insert New Action Item
                $act = ActionItems::updateOrCreate([
                    'note_id' =>$note_id,
                    'what' => $request->what[$i],
                    'how' => $request->how[$i],
                    'due_date' => $request->due_date[$i],
                    'created_by' => auth()->user()->id,
                ]);
                if(!$act){
                    return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
                }
                foreach($request->who[$i] as $pic){
                    Pic::updateOrCreate([
                        'action_id' => Hashids::decode($act->id)[0],
                        'user_id' => Hashids::decode($pic)[0]
                    ]);
                }
            }
        }

        foreach($to_delete as $action_id){
            ActionItems::where(['id'=> Hashids::decode($action_id)[0]])->first()->delete();
        }
        return back()->with('success','Data <strong>berhasil</strong> diubah!');

        // dd(array(
        //     'request'=>$request,
        //     'existing'=>$existing_item,
        //     'new_item'=>$edited_item,
        //     'to_delete'=>$to_delete,
        //     'existing_pic'=>$existing_pic,
        // ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ActionItems  $actionItems
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActionItems $actionItems)
    {
        //
    }
}
