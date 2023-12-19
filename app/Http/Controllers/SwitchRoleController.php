<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Vinkla\Hashids\Facades\Hashids;

class SwitchRoleController extends Controller
{
    public function __invoke(String $id)
    {
        $role = Role::find(Hashids::decode($id)[0]);

        abort_unless(auth()->user()->hasRole($role->name), 404);
 
        auth()->user()->update(['current_role_id' => $role->id]);
 
        return to_route('home');
    }
}
