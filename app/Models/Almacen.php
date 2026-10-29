<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Almacen extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'almacenes';
    protected $primaryKey = 'clave';
    protected $fillable = ['nombre', 'id_company', 'id_sucursal', 'principal'];
    protected $hidden = ['principal_sucursal_id'];

    protected static function booted(): void
    {
        static::saving(function (Almacen $almacen): void {
            $sucursal = Sucursal::query()->find($almacen->id_sucursal);

            if ($sucursal !== null) {
                $almacen->id_company = $sucursal->id_company;
            }

            if ($almacen->principal) {
                $query = static::query()
                    ->where('id_sucursal', $almacen->id_sucursal)
                    ->where('principal', true);

                if ($almacen->exists) {
                    $query->where('clave', '<>', $almacen->getKey());
                }

                if ($query->exists()) {
                    throw ValidationException::withMessages([
                        'principal' => 'Esta sucursal ya tiene un almacén principal.',
                    ]);
                }
            }
        });
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id');
    }
}
