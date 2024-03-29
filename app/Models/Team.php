<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'satker_id',
        'created_by',
        'updated_by',
    ];

    public function satker()
    {
        return $this->belongsTo(MSatker::class, 'satker_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
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
