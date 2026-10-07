<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Runs each catalog sheet through its own Import class. Only sheets present in
 * the workbook and matched by name below are processed; execution respects the
 * dependency order of the physical sheet tabs (Familias -> Subfamilias ->
 * Sub-subfamilias -> Unidades -> Almacenes -> Proveedores -> Clientes ->
 * Vendedores -> Productos).
 */
class CatalogosImport implements WithMultipleSheets
{
    public function __construct(private readonly array $sheetNames)
    {
    }

    public function sheets(): array
    {
        $imports = [
            'familias' => FamiliasImport::class,
            'subfamilias' => SubFamiliasImport::class,
            'subsubfamilias' => SubSubFamiliasImport::class,
            'almacenes' => AlmacenesImport::class,
            'proveedores' => ProveedoresImport::class,
            'vendedores' => VendedoresImport::class,
            'productos' => ProductosImport::class,
            'clientes' => ClientesImport::class,
            'customers' => ClientesImport::class,
            'unidades' => UnidadMedidasImport::class,
            'unidadesmedida' => UnidadMedidasImport::class,
            'unidadesdemedida' => UnidadMedidasImport::class,
            'unidadesinventario' => UnidadMedidasImport::class,
        ];
        $order = [
            'familias',
            'subfamilias',
            'subsubfamilias',
            'unidades',
            'unidadesmedida',
            'unidadesdemedida',
            'unidadesinventario',
            'almacenes',
            'proveedores',
            'clientes',
            'customers',
            'vendedores',
            'productos',
        ];

        $matchedSheets = [];
        foreach ($this->sheetNames as $sheetName) {
            $normalizedName = $this->normalize($sheetName);
            $importClass = $imports[$normalizedName] ?? null;

            if ($importClass !== null) {
                $matchedSheets[$normalizedName] = [$sheetName, $importClass];
            }
        }

        if ($matchedSheets === []) {
            throw new CatalogImportException(
                'El archivo no contiene hojas de catálogos reconocidas. Hojas encontradas: '
                . implode(', ', $this->sheetNames)
            );
        }

        $sheets = [];
        foreach ($order as $normalizedName) {
            if (isset($matchedSheets[$normalizedName])) {
                [$sheetName, $importClass] = $matchedSheets[$normalizedName];
                $sheets[$sheetName] = new $importClass();
            }
        }

        return $sheets;
    }

    private function normalize(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;

        return strtolower((string) preg_replace('/[^a-z0-9]/i', '', $value));
    }
}
