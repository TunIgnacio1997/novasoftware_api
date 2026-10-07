<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'users';
    protected $fillable = [
        'name',
        'user',
        'email',
        'password',
        'rol_id',
        'sucursal_id',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sucursal() {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function vendedor()
    {
        return $this->hasOne(Vendedor::class, 'id_users');
    }
}
