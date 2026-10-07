<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Familias extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'familias';
    protected $fillable = ['nombre', 'comision'];

    public function subFamilias()
    {
        return $this->hasMany(SubFamilia::class, 'id_familia');
    }
}
