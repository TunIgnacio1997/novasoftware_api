<?php

namespace App\Actions\Inventario;

use Milon\Barcode\Facades\DNS1DFacade as DNS1D;

class GenerarBarcodeAction
{
    public function execute(string $codigo): string
    {
        return DNS1D::getBarcodePNG(
            $codigo,
            'C128',
            1,   // grosor barras
            28   // altura
        );
    }
}