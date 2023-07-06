<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'action_id',
        'user_id',
        'status'
    ];

    public function action()
    {
        return $this->belongsTo(ActionItems::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
