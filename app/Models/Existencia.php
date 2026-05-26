<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Existencia extends Model
{
    use HasFactory;

    protected $table = 'existencias';

    protected $fillable = [
        'id_producto',
        'id_almacen',
        'cantidad',
    ];

    public function producto(){
        return $this->belongsTo(Producto::class, 'id', 'id_producto');
    }
}
