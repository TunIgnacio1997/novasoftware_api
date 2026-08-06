<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    use HasFactory;

    Protected $table = 'ordenes_traslado';

    protected $fillable = [
        'claveot', 
        'fecha', 
        'notas', 
        'id_sucursal_origen', 
        'id_almacen_origen', 
        'id_sucursal_destino', 
        'id_almacen_destino', 
        'estatus', 
        'id_sucursal', 
        'id_usuario', 
        'fecha_recibido', 
        'id_usuario_recibio', 
        'fecha_cancelado', 
        'id_usuario_cancelo', 
        'motivo_cancelacion'
    ];

    public function sucursalOrigen(){
        return $this->belongsTo(Sucursal::class, 'id_sucursal_origen');
    }

    public function almacenOrigen(){
        return $this->belongsTo(Almacen::class, 'id_almacen_origen', 'clave');
    }

     public function sucursalDestino()
    {
        return $this->belongsTo(
            Sucursal::class,
            'id_sucursal_destino',
            'id'
        );
    }

    public function almacenDestino()
    {
        return $this->belongsTo(
            Almacen::class,
            'id_almacen_destino',
            'clave'
        );
    }

    public function usuario()
    {
        return $this->belongsTo(
            User::class,
            'id_usuario'
        );
    }
}
