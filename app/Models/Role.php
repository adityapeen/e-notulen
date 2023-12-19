<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Role extends SpatieRole
{
  
    public function id_hash()
    {
        return  Hashids::encode($this->id);
    }
    /**
     * Hash the ids
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // protected function id(): Attribute
    // {
    //     return  Attribute::make(
    //         get: fn ($value) => Hashids::encode($value)
    //     );
    // }
}

