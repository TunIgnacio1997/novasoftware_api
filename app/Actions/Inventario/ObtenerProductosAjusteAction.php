<?php

namespace App\Actions\Inventario;

use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ObtenerProductosAjusteAction
{
    public function execute(int $idAlmacen, ?string $search = null): Collection
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
                'productos.familia',
                'productos.location',
                DB::raw('COALESCE(existencias.cantidad, 0) as stock_actual')
            )
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('productos.item_name', 'like', "%$search%")
                  ->orWhere('productos.item_number', 'like', "%$search%");
            }))
            ->get();
    }
}