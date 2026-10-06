<?php

namespace Tests\Feature;

use App\Imports\AlmacenesImport;
use App\Imports\CatalogImportException;
use App\Imports\CatalogosImport;
use App\Imports\ClientesImport;
use App\Imports\ProductosImport;
use App\Imports\ProveedoresImport;
use App\Imports\SubFamiliasImport;
use App\Imports\SubSubFamiliasImport;
use App\Imports\VendedoresImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Tests\TestCase;

class CatalogImportMappingTest extends TestCase
{
    public function test_template_sheet_headings_are_normalized_for_import(): void
    {
        $productRow = $this->prepareRow(new ProductosImport(), [
            'Nombre del producto (*)',
            'Número de artículo / SKU',
            'Descripción',
            'Subfamilia',
            'Sub-subfamilia',
            'Proveedor (Nombre comercial)',
            'Unidad de medida (*)',
            'Precio de compra',
            'Precio de venta (*)',
            'Existencia inicial',
            'Máximo',
            'Mínimo',
            'Ubicación en almacén',
        ], [
            'Galletas',
            'SKU-1',
            'Descripción del producto',
            'Dulces',
            'Chocolate',
            'Proveedor Uno',
            'Pieza',
            10,
            15,
            4,
            20,
            2,
            'Pasillo 1',
        ]);

        $this->assertSame('Galletas', $productRow['item_name']);
        $this->assertSame('SKU-1', $productRow['item_number']);
        $this->assertSame('Descripción del producto', $productRow['description']);
        $this->assertSame('Dulces', $productRow['sub_familia']);
        $this->assertSame('Chocolate', $productRow['sub_sub_familia']);
        $this->assertSame('Proveedor Uno', $productRow['proveedor']);
        $this->assertSame('Pieza', $productRow['unidad_medida']);
        $this->assertSame(4, $productRow['existencia_inicial']);
        $this->assertSame(20, $productRow['maximo']);
        $this->assertSame(2, $productRow['minimo']);
        $this->assertSame('Pasillo 1', $productRow['location']);
    }

    public function test_numeric_product_sku_is_normalized_to_string(): void
    {
        $row = (new ProductosImport())->prepareForValidation([
            'item_number' => 7501011000001,
        ], 2);

        $this->assertSame('7501011000001', $row['item_number']);
    }

    public function test_other_template_sheets_map_their_spanish_headings(): void
    {
        $subFamilia = $this->prepareRow(new SubFamiliasImport(), [
            'Familia (*)',
            'Subfamilia (*)',
        ], ['Bebidas', 'Carbonatadas']);
        $this->assertSame('Carbonatadas', $subFamilia['nombre']);
        $this->assertFalse((new SubFamiliasImport())->isEmptyWhen([
            'subfamilia' => 'Carbonatadas',
        ]));

        $subSubFamilia = $this->prepareRow(new SubSubFamiliasImport(), [
            'Subfamilia (*)',
            'Sub-subfamilia',
        ], ['Papas', 'Onduladas']);
        $this->assertSame('Papas', $subSubFamilia['sub_familia']);
        $this->assertSame('Onduladas', $subSubFamilia['nombre']);
        $this->assertFalse((new SubSubFamiliasImport())->isEmptyWhen([
            'sub_subfamilia' => 'Onduladas',
        ]));

        $warehouse = $this->prepareRow(new AlmacenesImport(), [
            'Nombre (*)',
            'Sucursal',
            '¿Es almacén principal?',
        ], ['Bodega', 'Matriz', 'Sí']);
        $this->assertSame(1, $warehouse['principal']);

        $supplier = $this->prepareRow(new ProveedoresImport(), [
            'Número proveedor',
            'Razón social (*)',
            'Nombre comercial (*)',
            'Código postal',
            'Teléfono 1',
            'Días de crédito',
        ], ['01', 'Proveedor S.A.', 'Proveedor', '24000', '9810000000', 30]);
        $this->assertSame('01', $supplier['num_proveedor']);
        $this->assertSame('24000', $supplier['cod_post']);
        $this->assertSame(30, $supplier['dias']);

        $customer = $this->prepareRow(new ClientesImport(), [
            'Número cliente',
            'Razón social (*)',
            'Nombre comercial (*)',
            'Tipo de cliente',
            'Límite de crédito ($)',
            'Plazo de crédito (días)',
        ], ['1001', 'Cliente S.A.', 'Cliente', 'PUBLICO', 5000, 15]);
        $this->assertSame('1001', $customer['num_cliente']);
        $this->assertSame('PUBLICO', $customer['tipo']);
        $this->assertSame(5000, $customer['credito']);
        $this->assertSame(15, $customer['plazo']);

        $seller = $this->prepareRow(new VendedoresImport(), [
            'Clave',
            'Nombre (*)',
            'Teléfono',
            'Tipo/Sucursal',
        ], ['V01', 'Vendedor', '9810000000', 'TIENDA']);
        $this->assertSame('9810000000', $seller['telef']);
        $this->assertSame('TIENDA', $seller['tipo']);
    }

    public function test_workbook_tabs_are_imported_in_dependency_order(): void
    {
        $sheets = (new CatalogosImport([
            'Instrucciones',
            'Familias',
            'Subfamilias',
            'Sub-subfamilias',
            'Almacenes',
            'Proveedores',
            'Clientes',
            'Vendedores',
            'Productos',
        ]))->sheets();

        $this->assertSame([
            'Familias',
            'Subfamilias',
            'Sub-subfamilias',
            'Almacenes',
            'Proveedores',
            'Clientes',
            'Vendedores',
            'Productos',
        ], array_keys($sheets));
    }

    public function test_workbook_without_catalog_tabs_is_rejected(): void
    {
        $this->expectException(CatalogImportException::class);

        (new CatalogosImport(['Instrucciones']))->sheets();
    }

    private function prepareRow(object $import, array $headings, array $values): array
    {
        $keys = HeadingRowFormatter::format($headings);

        return $import->prepareForValidation(array_combine($keys, $values), 2);
    }
}
