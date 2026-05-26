<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    use HasFactory;
    protected $table = 'estatus';

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_estatus');
    }
}
