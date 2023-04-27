<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Evidence extends Model
{
    use HasFactory;
    protected $table = 'evidences';
    protected $fillable = [
        'action_id',
        'description',
        'file',
        'uploaded_by'
    ];

    public function action()
    {
        return $this->belongsTo(ActionItems::class);
    }

    public function user()
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



}
