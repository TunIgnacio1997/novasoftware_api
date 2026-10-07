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
            $sheetNames = IOFactory::load($filePath)->getSheetNames();

            DB::transaction(function () use ($filePath, $sheetNames) {
                $this->ensurePaymentTypes();

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

    private function ensurePaymentTypes(): void
    {
        foreach ([
            1 => 'EFECTIVO',
            2 => 'ANTICIPO',
            3 => 'CHEQUE',
            4 => 'TARJETA',
            5 => 'TRANSFERENCIA',
            6 => 'VALES',
        ] as $id => $description) {
            $paymentType = TipoPago::query()->find($id);

            if ($paymentType) {
                $existingDescriptions = [
                    strtoupper(trim((string) $paymentType->descripcion)),
                    strtoupper(trim((string) $paymentType->descripcion2)),
                ];

                if (! in_array($description, $existingDescriptions, true)) {
                    throw new CatalogImportException(
                        "No se puede asignar {$description} al ID {$id}: ese ID ya pertenece a otro tipo de pago."
                    );
                }
            } else {
                $duplicate = TipoPago::query()
                    ->where(function ($query) use ($description) {
                        $query->where('descripcion', $description)
                            ->orWhere('descripcion2', $description);
                    })
                    ->first();

                if ($duplicate) {
                    throw new CatalogImportException(
                        "No se puede asignar {$description} al ID {$id}: ya existe con el ID {$duplicate->id}."
                    );
                }

                $paymentType = new TipoPago();
                $paymentType->id = $id;
            }

            $paymentType->descripcion = $description;
            $paymentType->descripcion2 = $description;
            $paymentType->controlado = 1;
            $paymentType->orden = $id;
            $paymentType->save();
        }
    }
}
