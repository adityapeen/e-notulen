<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_id',
        'user_id',
        'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function action()
    {
        return $this->belongsTo(ActionItems::class,'action_id');
    }

    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }
}
