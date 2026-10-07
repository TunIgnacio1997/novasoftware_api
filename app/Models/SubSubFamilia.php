<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSubFamilia extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sub_sub_familias';
    protected $fillable = ['nombre', 'id_sub_familia'];

    public function subFamilia()
    {
        return $this->belongsTo(SubFamilia::class, 'id_sub_familia');
    }
}
