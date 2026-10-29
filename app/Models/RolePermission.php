<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'module',
        'view',
        'create',
        'edit',
        'delete',
    ];

    protected $casts = [
        'view' => 'boolean',
        'create' => 'boolean',
        'edit' => 'boolean',
        'delete' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
