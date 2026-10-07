<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'productos';

        protected $fillable = [
            'item_name',
            'item_number',
            'description',
            'unit_m',
            'buy_price',
            'unit_price',
            'familia',
            'sub_familia',
            'sub_sub_familia',
            'id_familia',
            'id_sub_familia',
            'id_sub_sub_familia',
            'id_unidad_medida',
            'supplier_id',
            'maximo',
            'minimo',
            'location',
            'allow_core',
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
