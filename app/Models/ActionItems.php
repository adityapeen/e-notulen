<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class ActionItems extends Model
{
    use HasFactory;
    protected $fillable = [
        'note_id',
        'what',
        'how',
        'due_date',
        'done_date',
        'status',
        'created_by',
        'updated_by',
    ];

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class);
    }
    public function evidences()
    {
        return $this->hasMany(Evidence::class, 'action_id');
    }
    public function pics()
    {
        return $this->hasMany(Pic::class, 'action_id');
    }
    public function team(): HasManyThrough
    {
        return $this->hasManyThrough(Note::class, Team::class, 'action_id', 'team_id');
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
