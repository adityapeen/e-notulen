<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Note extends Model
{
    use HasFactory;
    protected $fillable = [
        'agenda_id',
        'type',
        'name',
        'date',
        'start_time',
        'end_time',
        'max_execute',
        'issues',
        'link_drive_notulen',
        'file_notulen',
        'status',
        'created_by',
        'updated_by'
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class);
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
    public function agenda_hash()
    {
        return  Hashids::encode($this->agenda_id);
    }

}
