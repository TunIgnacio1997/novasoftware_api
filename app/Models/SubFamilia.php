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

    public function familia()
    {
        return $this->belongsTo(Familias::class, 'id_familia');
    }

    public function subSubFamilias()
    {
        return $this->hasMany(SubSubFamilia::class, 'id_sub_familia');
    }
}
