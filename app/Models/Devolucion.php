<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'id_almacen',
        'id_usuario',
        'id_fecha',
        'fecha',
        'id_orden',
        'tipo',
        'estatus'
    ];

    public $timestamps = false;

    public function detalles()
    {
        return $this->hasMany(DetalleDevolucion::class, 'id_devolucion');
    }

    public function usuario()
    {
        return $this->belongsTo(
            User::class,
            'id_usuario'
        );
    }

    public function almacen()
    {
        return $this->belongsTo(
            Almacen::class,
            'id_almacen',
            'clave'
        );
    }
}