<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Agenda extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'icon_material',
        'date',
        'group_id',
        'priority_id',
        'satker_id',
        'docs_template_id',
        'summary',
        'created_by',
        'updated_by',
    ];

    public function group()
    {
        return $this->belongsTo(MGroup::class);
    }
    public function priority()
    {
        return $this->belongsTo(MPriority::class);
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
        return $this->hasMany(Note::class, 'agenda_id');
    }

    public function group_id_hash()
    {
        return   Hashids::encode($this->group_id);
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
