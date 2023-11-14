<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pic;
use App\Models\User;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class PerformanceController extends Controller
{
    public function index()
    {
        $title = "Performance";
        $data = Pic::select('user_id')
                ->selectRaw('SUM(CASE WHEN status = "todo" THEN 1 ELSE 0 END) as todo_count')
                ->selectRaw('SUM(CASE WHEN status = "onprogress" THEN 1 ELSE 0 END) as onprogress_count')
                ->selectRaw('SUM(CASE WHEN status = "done" THEN 1 ELSE 0 END) as done_count')
                ->selectRaw('AVG(performance) as performance_avg')
                ->groupBy('user_id')
                ->orderBy('done_count','DESC')
                ->paginate(15);

        return view('admin.performance.index', compact(['title','data']));
    }

    public function employee(String $hashed_id)
    {
        $title = "Employee Performance";
        $user_id = Hashids::decode($hashed_id)[0];
        $user = User::find($user_id);
        $summary = Pic::select('user_id')
                ->selectRaw('SUM(CASE WHEN status = "todo" THEN 1 ELSE 0 END) as todo_count')
                ->selectRaw('SUM(CASE WHEN status = "onprogress" THEN 1 ELSE 0 END) as onprogress_count')
                ->selectRaw('SUM(CASE WHEN status = "done" THEN 1 ELSE 0 END) as done_count')
                ->selectRaw('AVG(performance) as performance_avg')
                ->groupBy('user_id')
                ->where('user_id', $user_id)
                ->get();
        $tasks = Pic::where('user_id',$user_id)->paginate(15);

        return view('admin.performance.employee', compact(['title','user','summary','tasks']));
    }
}
