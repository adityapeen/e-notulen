<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MomRecipients extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'user_id',
        'mom_sent',
        'message_id'
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function getHashedNoteIdAttribute()
    {
        return   Hashids::encode($this->note_id);
    }

    function getHashedUserIdAttribute()
    {
        return   Hashids::encode($this->user_id);
    }

    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    // /**
    //  * Hash the ids
    //  *
    //  * @return \Illuminate\Database\Eloquent\Casts\Attribute
    //  */
    // protected function id(): Attribute
    // {
    //     return  Attribute::make(
    //         get: fn ($value) => Hashids::encode($value)
    //     );
    // }
}
