<?php

namespace App\Http\Controllers\AdminSatker;

use App\Models\Evidence;
use App\Http\Controllers\Controller;
use App\Models\ActionItems;
use App\Models\Pic;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class SatkerEvidenceController extends Controller
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

    public function add(String $hashed_id)
    {
        $title = "Tambah Eviden Action Item";
        $action_id = Hashids::decode($hashed_id)[0];
        // dd($action_id);
        $action = ActionItems::findOrFail($action_id);
        $evidences = Evidence::where('action_id', $action_id)->get();
        return view('satker.evidence.create', compact(['title','action','hashed_id','evidences']));
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
            'description' => ['required'],
            'file' => ['required','max:10000']
        ]);

        if ($request->file('file') != NULL) {            
            $file = $request->file('file'); // menyimpan data file yang diupload ke variabel $file
            $base_name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $nama_file =$base_name.'_'.time().'.'.$file->getClientOriginalExtension(); // add timestamp to filename
            
            $tujuan_upload = 'eviden'; // isi dengan nama folder tempat kemana file diupload
            $file->move($tujuan_upload,$nama_file);
        }
        else {
            $nama_file = NULL;
        }
        
        $action_id = Hashids::decode($request->action_id)[0];
        $evidence = Evidence::updateOrCreate([
                'action_id' => $action_id,
                'description' => $request->description,
                'file' => $nama_file,
                'uploaded_by' => auth()->user()->id,
        ]);
        if($evidence){
            ActionItems::findOrFail($action_id)->update(['status'=>'onprogress']);
            $pic = Pic::where([['action_id','=', $action_id],['user_id','=',auth()->user()->id]])->first();
            if( $pic != NULL){
                $pic->update(['status'=>'onprogress']);
            }            return redirect()->route("satker.notes.evidence",$request->action_id)->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Evidence  $evidence
     * @return \Illuminate\Http\Response
     */
    public function show(Evidence $evidence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Evidence  $evidence
     * @return \Illuminate\Http\Response
     */
    public function edit(String $hashed_id)
    {
        $title = "Edit Eviden Action Item";
        $id = Hashids::decode($hashed_id); //decode the hashed id
        $evidence = Evidence::find($id[0]);

        return view('satker.evidence.edit', compact('title','evidence'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evidence  $evidence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, String $hashed_id)
    {
        $request->validate([
            'description' => ['required'],
            // 'file' => ['required','max:10000']
        ]);
        $id = Hashids::decode($hashed_id);
        $evidence = Evidence::findOrFail($id)->first();

        if ($request->hasFile('file')) {
            $directory = 'eviden'; // isi dengan nama folder tempat kemana file diupload
            if($evidence->file != NULL){
                try {
                    $file_path = realpath($directory . '/' . $evidence->file);
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                } catch (Throwable $e) {
                    $e;
                }         
            }
            $file = $request->file('file'); // menyimpan data file yang diupload ke variabel $file
            $base_name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $nama_file =$base_name.'_'.time().'.'.$file->getClientOriginalExtension(); // add timestamp to filename
            $file->move($directory,$nama_file);      
        }
        else {
            $nama_file = $evidence->file;
        }

        $evidence->update([
            'description' => $request->description,
            'file' => $nama_file,
            'updated_by' => auth()->user()->id,
        ]) ;
        if($evidence){
            return redirect()->route("satker.notes.evidence",$evidence->action->id)->with('success','Data <strong>berhasil</strong> disimpan');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> ditambahkan!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Evidence  $evidence
     * @return \Illuminate\Http\Response
     */
    public function destroy(String $hashed_id)
    {
        $evidence = Evidence::findOrFail(Hashids::decode($hashed_id)[0]);
        $action_id = $evidence->action_id;
        $directory = 'eviden';
        try {
            $file_path = realpath($directory . '/' . $evidence->file);
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        } catch (Throwable $e) {
            $e;
        }   
        if($evidence->delete()){
            $ev_count = Evidence::where('action_id', $action_id)->count();
            if($ev_count == 0 ){
                ActionItems::findOrFail($action_id)->update(['status'=>'todo']);
            }
            return back()->with('success','Data <strong>berhasil</strong> dihapus!');
        }else{
            return back()->withErrors(['Data <strong>gagal</strong> dihapus!']);
        }
    }
}
