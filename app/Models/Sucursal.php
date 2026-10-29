<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sucursal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre', 'logo', 'slogan', 'background', 'tel', 'tel2', 'correo', 'direccion', 'colonia', 'ciudad', 'estado', 'id_company', 'id_fecha', 'id_usuario'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'id_company', 'id');
    }

    public function almacenes(): HasMany
    {
        return $this->hasMany(Almacen::class, 'id_sucursal', 'id');
    }

    protected static function booted(): void
    {
        static::creating(function (Sucursal $sucursal): void {
            $company = Company::find($sucursal->id_company) ?? Company::query()->first();

            if ($company) {
                $sucursal->id_company ??= $company->id;
                $sucursal->logo ??= $company->logo;
                $sucursal->correo ??= $company->mail;
                $sucursal->direccion ??= $company->direccion;
            }

            foreach ([
                'logo',
                'slogan',
                'background',
                'tel',
                'tel2',
                'correo',
                'direccion',
                'colonia',
                'ciudad',
                'estado',
            ] as $field) {
                $sucursal->{$field} = $sucursal->{$field} ?? '';
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
