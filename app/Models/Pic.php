<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Pic extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'action_id',
        'user_id',
        'status',
        'done_date',
        'performance'
    ];

    public function action()
    {
        return $this->belongsTo(ActionItems::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_hash()
    {
        return  Hashids::encode($this->user_id);
    }

    /**
     * Hash the ids
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function id(): Attribute
    {
        return  Attribute::make(
            get: fn ($value) => Hashids::encode($value)
        );
    }
}
