<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'satker_id',
        'current_role_id',
        'level_id',
        'team_id',
        'phone',
        'status',
        'last_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function satker()
    {
        return $this->belongsTo(MSatker::class);
    }

    public function level()
    {
        return $this->belongsTo(MLevel::class);
    }

    public function pics()
    {
        return $this->hasMany(Pic::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function currentRole()
    {
        return $this->belongsTo(Role::class, 'current_role_id');
    }

    /**
     * Hash the ids
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    function id_hash()
    {
        return   Hashids::encode($this->id);
    }

    public function team_id_hash()
    {
        return   Hashids::encode($this->team_id);
    }

    public function role_hash()
    {
        return   Hashids::encode($this->current_role_id);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
