<?php

namespace App\Actions\Inventario;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class ObtenerInventarioInicialAction
{
    public function execute(int $idAlmacen, ?string $familia = null)
    {
        return Producto::query()
            ->leftJoin('existencias', function ($join) use ($idAlmacen) {
                $join->on('productos.id', '=', 'existencias.id_producto')
                    ->where('existencias.id_almacen', $idAlmacen);
            })
            ->select(
                'productos.id',
                'productos.item_name',
                'productos.item_number',
                'productos.description',
                'productos.unit_price',
                'productos.familia',
                'productos.location',
                DB::raw('COALESCE(existencias.cantidad, 0) as existencia')
            )
            ->when($familia && $familia !== 'Todas', function ($query) use ($familia) {
                $query->where('productos.familia', $familia);
            })
            ->get();
    }
}