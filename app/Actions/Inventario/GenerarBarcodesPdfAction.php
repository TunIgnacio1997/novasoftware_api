<?php
namespace App\Actions\Inventario;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class GenerarBarcodesPdfAction
{
    public function __construct(
        protected GenerarBarcodeAction $barcodeAction
    ) {}

    public function execute(array $productos): string
    {
        $items = [];

        foreach ($productos as $producto) {

            $model = Producto::find($producto['id_producto']);

            $items[] = [
                'nombre'  => $model->item_name,
                'codigo'  => $model->item_number,
                'barcode' => $this->barcodeAction
                    ->execute($model->item_number),
            ];
        }

        $pdf = Pdf::loadView(
            'pdf.barcodes',
            compact('items')
        );

        $path = 'barcodes/inventario_' . time() . '.pdf';

        Storage::disk('public')->put(
            $path,
            $pdf->output()
        );

        return $path;
    }
}