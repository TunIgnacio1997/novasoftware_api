<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

        protected $fillable = [
            'item_name',
            'codigo',
            'descripcion',
            'precio',
            'id_categoria',
            'id_marca',
            'id_unidad_medida',
            'id_sucursal',
            'id_almacen'
        ];
    protected $appends = ['stock'];

    public function imagenes()
    {
        return $this->hasMany(ImagenesProducto::class, 'id_producto', 'id');
    }

    public function existencias()
    {
        return $this->hasMany(Existencia::class, 'id_producto', 'id');
    }

    public function getStockAttribute()
    {
        return $this->existencias->sum('cantidad');
    }
}
