<?php

namespace App\Imports;

use App\Models\Almacen;
use App\Models\Existencia;
use App\Models\Familias;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\SubFamilia;
use App\Models\SubSubFamilia;
use App\Models\UnidadMedida;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

/**
 * Sheet "Productos". Supports both internal headings and the Spanish initialization template.
 */
class ProductosImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function prepareForValidation(array $data, int $index): array
    {
        $aliases = [
            'item_number' => ['numero_de_articulo_sku', 'sku'],
            'item_name' => ['nombre_del_producto'],
            'description' => ['descripcion'],
            'sub_familia' => ['subfamilia'],
            'sub_sub_familia' => ['sub_subfamilia'],
            'proveedor' => ['proveedor_nombre_comercial', 'num_proveedor', 'numero_proveedor'],
            'unidad_medida' => ['unidad_de_medida'],
            'buy_price' => ['precio_de_compra'],
            'unit_price' => ['precio_de_venta'],
            'maximo' => ['nivel_maximo'],
            'location' => ['ubicacion_en_almacen'],
        ];

        foreach ($aliases as $field => $alternatives) {
            if (! isset($data[$field]) || $data[$field] === '') {
                foreach ($alternatives as $alternative) {
                    if (isset($data[$alternative]) && $data[$alternative] !== '') {
                        $data[$field] = $data[$alternative];
                        break;
                    }
                }
            }
        }

        $itemNumber = $data['item_number'] ?? null;
        if (is_int($itemNumber)) {
            $data['item_number'] = (string) $itemNumber;
        } elseif (is_float($itemNumber) && is_finite($itemNumber) && floor($itemNumber) === $itemNumber) {
            $data['item_number'] = number_format($itemNumber, 0, '.', '');
        }

        return $data;
    }

    public function model(array $row)
    {
        $itemNumber = trim((string) ($row['item_number'] ?? ''));
        $itemName = trim((string) ($row['item_name'] ?? ''));

        if ($itemNumber === '') {
            return null;
        }

        $familiaNombre = trim((string) ($row['familia'] ?? ''));
        $familia = Familias::firstOrCreate(
            ['nombre' => $familiaNombre],
            ['comision' => 0]
        );
        $subFamiliaNombre = trim((string) ($row['sub_familia'] ?? ''));
        $subFamilia = SubFamilia::where('nombre', $subFamiliaNombre)
            ->where(fn ($q) => $q->where('id_familia', $familia->id)->orWhereNull('id_familia'))
            ->orderByRaw('id_familia is null')
            ->first();
        if ($subFamilia && $subFamilia->id_familia === null) {
            $subFamilia->update(['id_familia' => $familia->id]);
        }
        if (! $subFamilia) {
            throw new CatalogImportException(
                "Producto '{$itemNumber}': la subfamilia '{$subFamiliaNombre}' no pertenece a la familia '{$familia->nombre}'."
            );
        }

        $subSubFamiliaNombre = trim((string) ($row['sub_sub_familia'] ?? ''));
        $subSubFamilia = null;
        if ($subSubFamiliaNombre !== '') {
            $subSubFamilia = SubSubFamilia::where('nombre', $subSubFamiliaNombre)
                ->where('id_sub_familia', $subFamilia->id)
                ->first();
            if (! $subSubFamilia) {
                throw new CatalogImportException(
                    "Producto '{$itemNumber}': la sub-subfamilia '{$subSubFamiliaNombre}' no pertenece a la subfamilia '{$subFamiliaNombre}'."
                );
            }
        }

        $unidadNombre = trim((string) ($row['unidad_medida'] ?? ''));
        $unidadMedida = UnidadMedida::firstOrCreate(['nombre' => $unidadNombre], ['created_at' => now()]);

        $proveedorNombre = trim((string) ($row['proveedor'] ?? ''));
        $proveedor = null;
        if ($proveedorNombre !== '') {
            $proveedor = Proveedor::where('num_proveedor', $proveedorNombre)
                ->orWhere('nombre_comercial', $proveedorNombre)
                ->first();
            if (! $proveedor) {
                throw new CatalogImportException(
                    "Producto '{$itemNumber}': no existe el proveedor '{$proveedorNombre}'."
                );
            }
        }

        $producto = Producto::updateOrCreate(
            ['item_number' => $itemNumber],
            [
                'item_name' => $itemName,
                'description' => $row['description'] ?? null,
                'familia' => $familia->nombre,
                'sub_familia' => $subFamilia->nombre,
                'sub_sub_familia' => $subSubFamilia?->nombre ?? '',
                'id_familia' => $familia->id,
                'id_sub_familia' => $subFamilia->id,
                'id_sub_sub_familia' => $subSubFamilia?->id,
                'unit_m' => $unidadMedida->nombre,
                'id_unidad_medida' => $unidadMedida->id,
                'supplier_id' => $proveedor->id ?? 0,
                'buy_price' => $this->toDecimal($row['buy_price'] ?? null) ?? 0,
                'unit_price' => $this->toDecimal($row['unit_price'] ?? null) ?? 0,
                'maximo' => $this->toDecimal($row['maximo'] ?? null) ?? 0,
                'minimo' => $this->toDecimal($row['minimo'] ?? null) ?? 0,
                'location' => $row['location'] ?? null,
                'allow_core' => (int) ($row['allow_core'] ?? 0),
            ]
        );

        $existenciaInicial = $this->toDecimal($row['existencia_inicial'] ?? null);
        if ($existenciaInicial !== null && $existenciaInicial > 0) {
            $almacenesPrincipales = Almacen::where('principal', 1)->limit(2)->get(['clave']);
            if ($almacenesPrincipales->count() !== 1) {
                throw new CatalogImportException(
                    "Producto '{$itemNumber}': debe existir exactamente un almacén principal para registrar la existencia inicial."
                );
            }

            Existencia::firstOrCreate(
                [
                    'id_producto' => $producto->id,
                    'id_almacen' => $almacenesPrincipales->first()->clave,
                ],
                ['cantidad' => $existenciaInicial]
            );
        }

        return $producto;
    }

    public function rules(): array
    {
        return [
            'item_number' => ['required', 'string', 'max:20'],
            'item_name' => ['required', 'string', 'max:100'],
            'familia' => ['required', 'string', 'max:50'],
            'sub_familia' => ['required', 'string', 'max:50'],
            'unidad_medida' => ['required', 'string', 'max:50'],
            'buy_price' => ['nullable', 'numeric'],
            'unit_price' => ['required', 'numeric'],
            'maximo' => ['nullable', 'numeric', 'min:0'],
            'minimo' => ['nullable', 'numeric', 'min:0'],
            'existencia_inicial' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function isEmptyWhen(array $row): bool
    {
        return trim((string) ($row['item_number'] ?? $row['numero_de_articulo_sku'] ?? '')) === '';
    }

    private function toDecimal(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }
}
