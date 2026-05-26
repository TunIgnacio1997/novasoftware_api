<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class InventarioAjuste extends Model
{
    protected $table = 'inventario_ajuste';
    protected $primaryKey = 'id_ajuste';
    public $timestamps = false;

    protected $fillable = [
        'estatus',
        'fecha',
        'id_sucursal',
        'id_almacen',
        'familia',
        'subfamilia',
        'solo_e',
        'responsable',
        'observaciones',
        'productos',
        'id_usuario',
        'id_fecha',
        'autorizo',
        'pdf_barcodes'
    ];

    public function detalles()
    {
        return $this->hasMany(InventarioDetalle::class, 'id_inventario', 'id_ajuste');
    }
}
