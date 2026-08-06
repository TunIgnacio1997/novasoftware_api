<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;
    protected $table = 'otros_gastos';
    protected $primaryKey = 'folio';
    public $timestamps = false; // Ajustar según si manejas created_at/updated_at

    protected $fillable = [
        'id_sucursal',
        'nomina',
        'fecha',
        'factura_nota',
        'costo',
        'tipo_pago',
        'observaciones',
        'estatus',
    ];

    public function paymentType()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago', 'id');
    }
}
