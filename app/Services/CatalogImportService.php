<?php

namespace App\Services;

use App\Imports\CatalogImportException;
use App\Imports\CatalogosImport;
use App\Models\Estatus;
use App\Models\TipoPago;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetReaderException;

class CatalogImportService
{
    /**
     * Imports the initial catalogs workbook. Wrapped in a transaction so a
     * failure on any row/sheet rolls back the whole batch.
     *
     * @throws \Throwable
     */
    public function import(string $filePath): void
    {
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $sheetNames = $reader->listWorksheetNames($filePath);

            DB::transaction(function () use ($filePath, $sheetNames) {
                $hasCashPaymentType = TipoPago::query()
                    ->where('descripcion', 'Efectivo')
                    ->orWhere('descripcion2', 'Efectivo')
                    ->exists();

                if (! $hasCashPaymentType) {
                    TipoPago::create([
                        'descripcion' => 'Efectivo',
                        'descripcion2' => 'Efectivo',
                        'controlado' => 1,
                        'orden' => 1,
                    ]);
                }

                foreach ([
                    ['descripcion' => 'Proceso', 'tipo' => 'venta'],
                    ['descripcion' => 'Proceso', 'tipo' => 'compra'],
                    ['descripcion' => 'Completado', 'tipo' => 'venta'],
                    ['descripcion' => 'Completado', 'tipo' => 'compra'],
                    ['descripcion' => 'Cancelado', 'tipo' => 'venta'],
                    ['descripcion' => 'Cancelado', 'tipo' => 'compra'],
                    ['descripcion' => 'Recibido', 'tipo' => 'compra'],
                ] as $statusAttributes) {
                    Estatus::query()->firstOrCreate($statusAttributes);
                }

                Excel::import(new CatalogosImport($sheetNames), $filePath);
            });
        } catch (SpreadsheetReaderException $exception) {
            throw new CatalogImportException(
                'El archivo no es un libro Excel válido o está dañado.',
                previous: $exception
            );
        }
    }
}
