<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    use HasFactory;
    protected $table = 'vendedores';

    public function venta(){
        return $this->hasOne(Venta::class, 'id_vendedor');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_users');
    }
}
