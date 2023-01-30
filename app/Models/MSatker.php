<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MSatker extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name'
    ];
    public $timestamps = false;

}
