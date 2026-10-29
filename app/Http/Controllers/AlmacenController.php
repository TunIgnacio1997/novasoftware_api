<?php
namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Sucursal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class AlmacenController extends Controller
{
    private const ALMACEN_REFERENCES = [
        ['cotizaciones', 'id_almacen'],
        ['det_items', 'id_almacen'],
        ['devoluciones', 'id_almacen'],
        ['existencias', 'id_almacen'],
        ['inventario_ajuste', 'id_almacen'],
        ['mermas', 'id_almacen'],
        ['movimientos', 'id_almacen'],
        ['ordenes_compra', 'id_almacen'],
        ['ordenes_produccion', 'id_almacen'],
        ['ordenes_traslado', 'id_almacen_origen'],
        ['ordenes_traslado', 'id_almacen_destino'],
        ['ventas', 'id_almacen'],
    ];

    public function getAlmacenes(Request $request)
    {
        return Almacen::when($request->filled('id'), function ($query) use ($request) {
                $query->where('clave', $request->id);
            })
            ->when($request->filled('nombre'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->nombre . '%');
            })
            ->orderBy('clave', 'desc')
            ->paginate($request->itemPage);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'id_sucursal' => ['required', 'integer', 'exists:sucursales,id'],
            'principal' => ['sometimes', 'required', 'boolean'],
        ]);
        $data['principal'] = (bool) ($data['principal'] ?? false);

        $almacen = DB::transaction(function () use ($data): Almacen {
            $sucursal = Sucursal::query()
                ->whereKey($data['id_sucursal'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($data['principal']) {
                $this->ensureNoOtherPrincipal($sucursal->id);
            }

            $almacen = new Almacen([
                'nombre' => $data['nombre'],
                'id_sucursal' => $sucursal->id,
                'principal' => $data['principal'],
            ]);
            $almacen->save();

            return $almacen->load('sucursal');
        });

        return response()->json($almacen, 201);
    }

    public function update(Request $request, Almacen $almacen): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:50'],
            'id_sucursal' => ['sometimes', 'required', 'integer', 'exists:sucursales,id'],
            'principal' => ['sometimes', 'required', 'boolean'],
        ]);

        $almacen = DB::transaction(function () use ($data, $almacen): Almacen {
            $targetSucursalId = (int) ($data['id_sucursal'] ?? $almacen->id_sucursal);
            Sucursal::query()
                ->whereKey($targetSucursalId)
                ->lockForUpdate()
                ->firstOrFail();

            $current = Almacen::query()
                ->lockForUpdate()
                ->findOrFail($almacen->getKey());

            if (
                !array_key_exists('id_sucursal', $data)
                && (int) $current->id_sucursal !== $targetSucursalId
            ) {
                throw ValidationException::withMessages([
                    'id_sucursal' => 'La sucursal del almacén cambió. Recarga el registro e inténtalo de nuevo.',
                ]);
            }

            $willBePrincipal = (bool) ($data['principal'] ?? $current->principal);
            if ($willBePrincipal) {
                $this->ensureNoOtherPrincipal($targetSucursalId, (int) $current->getKey());
            }

            $current->fill($data);
            $current->save();

            return $current->load('sucursal');
        });

        return response()->json($almacen);
    }

    public function destroy(Almacen $almacen): JsonResponse
    {
        $deleted = DB::transaction(function () use ($almacen): bool {
            $current = Almacen::query()
                ->lockForUpdate()
                ->findOrFail($almacen->getKey());

            Sucursal::query()
                ->whereKey($current->id_sucursal)
                ->lockForUpdate()
                ->firstOrFail();

            foreach (self::ALMACEN_REFERENCES as [$table, $column]) {
                if (
                    Schema::hasTable($table)
                    && Schema::hasColumn($table, $column)
                    && DB::table($table)->where($column, $current->getKey())->exists()
                ) {
                    return false;
                }
            }

            $current->delete();

            return true;
        });

        if (!$deleted) {
            return response()->json([
                'message' => 'No se puede eliminar el almacén porque tiene registros asociados.',
            ], 409);
        }

        return response()->json([
            'message' => 'Almacén eliminado correctamente.',
        ]);
    }

    private function ensureNoOtherPrincipal(int $sucursalId, ?int $exceptAlmacenId = null): void
    {
        $query = Almacen::query()
            ->where('id_sucursal', $sucursalId)
            ->where('principal', true)
            ->lockForUpdate();

        if ($exceptAlmacenId !== null) {
            $query->where('clave', '<>', $exceptAlmacenId);
        }

        if ($query->first() !== null) {
            throw ValidationException::withMessages([
                'principal' => 'Esta sucursal ya tiene un almacén principal.',
            ]);
        }
    }
}
