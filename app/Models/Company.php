<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;
    protected $table = 'company';

    protected $fillable = [
        'id',
        'conceptnamecompany',
        'mail',
        'url',
        'logo',
        'access_key',
        'direccion',
        'iva',
        'rfc',
        'regimen_fiscal',
        'onboarding_completed',
        'catalog_import_mode'
    ];

    // Se incluye automáticamente en el JSON
    protected $appends = ['logo_url'];

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class, 'id_company', 'id');
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->logo ? asset('storage/' . ltrim($this->logo, '/')) : null
        );
    }
}
