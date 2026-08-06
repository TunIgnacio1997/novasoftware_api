<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;
    protected $table = 'otras_entradas';
    protected $primaryKey = 'id';
    public $timestamps = false; // Ajustar si aplica timestamping en la DB

    protected $fillable = [
        'id_sucursal',
        'fecha',
        'cantidad',
        'tipo_pago',
        'acreedor',
        'vencimiento',
        'nota',
        'estatus',
    ];

    public function paymentType()
    {
        return $this->belongsTo(TipoPago::class, 'tipo_pago', 'id');
    }
}
