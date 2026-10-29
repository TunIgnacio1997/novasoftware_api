<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'role_id',
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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function hasPermission(string $module, string $action): bool
    {
        if (! in_array($action, ['view', 'create', 'edit', 'delete'], true)) {
            return false;
        }

        $role = $this->role ?? $this->rol;
        $permission = $role?->permissions()->where('module', $module)->first();

        return $permission !== null && (bool) $permission->getAttribute($action);
    }

    public function vendedor()
    {
        return $this->hasOne(Vendedor::class, 'id_users');
    }
}
