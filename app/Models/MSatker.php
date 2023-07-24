<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class MSatker extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name'
    ];
    public $timestamps = false;

    function id_hash()
    {
        return   Hashids::encode($this->id);
    }

}
