<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'agenda_id',
        'user_id'
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
