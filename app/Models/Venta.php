<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;
    protected $table = 'ventas';

    protected $primaryKey = 'id_venta';
    
    protected $fillable = [
        'folio_venta',
        'id_cliente',
        'id_estatus',
        'id_tipo_pago',
        'serie',
        'fecha_registro',
        'importe',
        'iva_aplicado',
        'id_usuario',
        'impreso',
        'obs',
        'id_sucursal',
        'id_almacen',
        'status_xml',
        'nombre_xml',
        'descuento',
        'id_vendedor',
        'referencia'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function estatus(){
        return $this->belongsTo(Estatus::class, 'id_estatus');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function vendedorPorUsuario()
    {
        return $this->hasOneThrough(
            Vendedor::class,  // modelo final
            User::class,      // modelo intermedio
            'id',             // PK de User
            'id_users',       // FK en Vendedor
            'id_usuario',     // FK en Venta
            'id'              // PK de User
        );
    }

    public function sucursal(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function productos(){
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }

    protected static function booted()
    {
        static::creating(function ($venta) {

            $ultimo = Venta::max('folio_venta') + 1;

            $venta->folio_venta = str_pad($ultimo, 8, '0', STR_PAD_LEFT);

        });
    }
}
