<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubFamilia extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sub_familias';
    protected $fillable = ['nombre', 'id_familia'];

    public static function firstOrCreateForFamily(string $nombre, int $familiaId): self
    {
        $subFamilia = static::query()
            ->where('nombre', $nombre)
            ->where('id_familia', $familiaId)
            ->first();

        if ($subFamilia) {
            return $subFamilia;
        }

        $subFamiliaSinFamilia = static::query()
            ->where('nombre', $nombre)
            ->whereNull('id_familia')
            ->first();

        if ($subFamiliaSinFamilia) {
            $subFamiliaSinFamilia->update(['id_familia' => $familiaId]);

            return $subFamiliaSinFamilia;
        }

        return static::create([
            'nombre' => $nombre,
            'id_familia' => $familiaId,
        ]);
    }

    public function familia()
    {
        return $this->belongsTo(Familias::class, 'id_familia');
    }

    public function subSubFamilias()
    {
        return $this->hasMany(SubSubFamilia::class, 'id_sub_familia');
    }
}
