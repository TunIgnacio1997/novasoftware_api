<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SucursalController extends Controller
{
    private const SUCURSAL_REFERENCES = [
        ['users', 'sucursal_id'],
        ['almacenes', 'id_sucursal'],
        ['cobratarios', 'id_sucursal'],
        ['corte_caja', 'id_sucursal'],
        ['cotizaciones', 'id_sucursal'],
        ['fondo_fijo', 'id_sucursal'],
        ['inventario_ajuste', 'id_sucursal'],
        ['items_serie', 'id_sucursal'],
        ['movimientos_cuentas_clientes', 'id_sucursal'],
        ['movimientos_cuentas_proveedores', 'id_sucursal'],
        ['ordenes_compra', 'id_sucursal'],
        ['otras_entradas', 'id_sucursal'],
        ['ordenes_produccion', 'id_sucursal'],
        ['ordenes_transferencia', 'sucursal_p'],
        ['ordenes_transferencia', 'sucursal_d'],
        ['ordenes_transferencia', 'id_sucursal'],
        ['otros_gastos', 'id_sucursal'],
        ['ordenes_traslado', 'id_sucursal_origen'],
        ['ordenes_traslado', 'id_sucursal_destino'],
        ['ordenes_traslado', 'id_sucursal'],
        ['reparticion', 'id_sucursal'],
        ['tipos_vendedores', 'id_sucursal'],
        ['tipos_vendedores_tipos_clientes', 'id_sucursal'],
        ['vendedores', 'id_sucursal'],
        ['ventas', 'id_sucursal'],
    ];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'itemPage' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $item = Sucursal::with('company')->orderBy('id', 'desc')->paginate($filters['itemPage'] ?? 15);

        return response()->json($item);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $sucursal = Sucursal::create($data);

        return response()->json($sucursal, 201);
    }

    public function update(Request $request, Sucursal $sucursal): JsonResponse
    {
        $data = $request->validate($this->rules(true));

        foreach (['logo', 'slogan', 'background', 'tel', 'tel2', 'correo', 'direccion', 'colonia', 'ciudad', 'estado'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === null) {
                $data[$field] = '';
            }
        }

        $sucursal->fill($data);
        $sucursal->save();

        return response()->json($sucursal);
    }

    public function destroy(Sucursal $sucursal): JsonResponse
    {
        foreach (self::SUCURSAL_REFERENCES as [$table, $column]) {
            if (
                Schema::hasTable($table)
                && Schema::hasColumn($table, $column)
                && DB::table($table)->where($column, $sucursal->id)->exists()
            ) {
                return response()->json([
                    'message' => 'No se puede eliminar la sucursal porque tiene registros asociados.',
                ], 409);
            }
        }

        $sucursal->delete();

        return response()->json([
            'message' => 'Sucursal eliminada correctamente.',
        ]);
    }

    private function rules(bool $partial = false): array
    {
        $nameRule = $partial ? 'sometimes' : 'required';

        return [
            'nombre' => [$nameRule, 'string', 'max:100'],
            'logo' => ['sometimes', 'nullable', 'string'],
            'slogan' => ['sometimes', 'nullable', 'string', 'max:100'],
            'background' => ['sometimes', 'nullable', 'string'],
            'tel' => ['sometimes', 'nullable', 'string', 'max:25'],
            'tel2' => ['sometimes', 'nullable', 'string', 'max:25'],
            'correo' => ['sometimes', 'nullable', 'string', 'max:25'],
            'direccion' => ['sometimes', 'nullable', 'string', 'max:100'],
            'colonia' => ['sometimes', 'nullable', 'string', 'max:50'],
            'ciudad' => ['sometimes', 'nullable', 'string', 'max:50'],
            'estado' => ['sometimes', 'nullable', 'string', 'max:50'],
            'id_company' => $partial
                ? ['sometimes', 'integer', 'exists:company,id']
                : ['sometimes', 'nullable', 'integer', 'exists:company,id'],
            'ciudad' => ['sometimes', 'nullable', 'string', 'max:50'],
            'estado' => ['sometimes', 'nullable', 'string', 'max:50'],
            'id_company' => $partial
                ? ['sometimes', 'integer', 'exists:company,id']
                : ['sometimes', 'nullable', 'integer', 'exists:company,id'],
            'id_fecha' => ['sometimes', 'nullable', 'date'],
            'id_usuario' => ['sometimes', 'nullable', 'integer'],
        ];
    }
}
