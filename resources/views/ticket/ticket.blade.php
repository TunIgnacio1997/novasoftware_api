<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket #{{ $venta->folio_venta }}</title>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        @page {
            size: 80mm auto; /* Tamaño estándar de ticket térmico de 80mm */
            margin: 0;
            background-color: #ffffff;
        }

        body {
            font-family: 'Courier New', Courier, 'Courier', monospace, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            margin: 0;
            padding: 12px;
            width: 80mm;
            background-color: #ffffff;
            line-height: 1.3;
        }

        .ticket {
            width: 100%;
        }

        /* Encabezado Principal */
        .header {
            text-align: center;
            border-bottom: 2px dashed #333333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .brand-name {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }

        .slogan {
            font-style: italic;
            font-size: 9px;
            color: #555555;
            margin-bottom: 6px;
        }

        .info-block {
            font-size: 9.5px;
            color: #333333;
            line-height: 1.25;
        }

        /* Bloque Resumen del Folio */
        .sale-info {
            background-color: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 6px 8px;
            margin-bottom: 10px;
            font-size: 10px;
        }

        .sale-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .sale-info td {
            padding: 1px 0;
        }

        .label {
            font-weight: bold;
            color: #444444;
        }

        /* Secciones Secundarias */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #cccccc;
            padding-bottom: 2px;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        /* Tabla de Productos */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        .items-table th {
            border-bottom: 1px solid #000000;
            border-top: 1px solid #000000;
            font-size: 9.5px;
            text-align: left;
            padding: 4px 0;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 5px 0 2px 0;
            font-size: 10px;
            vertical-align: top;
        }

        .item-name {
            font-weight: bold;
            display: block;
            font-size: 10.5px;
        }

        .item-subtext {
            font-size: 8.5px;
            color: #666666;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Totales */
        .totals-container {
            border-top: 1px dashed #333333;
            padding-top: 6px;
            margin-top: 6px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 2px 0;
            font-size: 10px;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 1px solid #000000;
            border-bottom: 2px solid #000000;
            padding: 4px 0 !important;
            margin-top: 4px;
        }

        /* Pie de página */
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #aaaaaa;
            font-size: 9px;
            color: #444444;
        }

        .status-badge {
            display: inline-block;
            background-color: #e2e8f0;
            color: #334155;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8.5px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="ticket">
    <!-- ENCABEZADO DE SUCURSAL -->
    <div class="header">
        <div class="brand-name">{{ $venta->sucursal->nombre ?? 'MI TIENDA' }}</div>
        @if(!empty($venta->sucursal->slogan))
            <div class="slogan">{{ $venta->sucursal->slogan }}</div>
        @endif
        <div class="info-block">
            {{ $venta->sucursal->direccion ?? '' }}<br>
            {{ $venta->sucursal->colonia ?? '' }}, {{ $venta->sucursal->ciudad ?? '' }}<br>
            Tel: {{ $venta->sucursal->tel ?? 'N/A' }}
            @if(!empty($venta->sucursal->correo))
                <br>Email: {{ $venta->sucursal->correo }}
            @endif
        </div>
    </div>

    <!-- INFORMACIÓN DE LA VENTA -->
    <div class="sale-info">
        <table>
            <tr>
                <td class="label">FOLIO:</td>
                <td class="text-right"><strong>#{{ $venta->folio_venta }}</strong></td>
            </tr>
            <tr>
                <td class="label">FECHA:</td>
                <td class="text-right">{{ \Carbon\Carbon::parse($venta->fecha_registro)->format('d/m/Y H:i') }}</td>
            </tr>
            <!--tr>
                <td class="label">ESTATUS:</td>
                <td class="text-right">
                    <span class="status-badge">{{ $venta->estatus->descripcion ?? 'N/A' }}</span>
                </td>
            </tr -->
        </table>
    </div>

    <!-- DATOS DE CLIENTE Y VENDEDOR -->
    <div style="margin-bottom: 8px;">
        <div class="section-title">DATOS GENERALES</div>
        <table style="width: 100%; font-size: 9.5px;">
            <tr>
                <td class="label" style="width: 60px;">Cliente:</td>
                <td>{{ $venta->cliente->razon_social ?? 'Público General' }} (#{{ $venta->cliente->num_cliente ?? 'N/A' }})</td>
            </tr>
            @if(!empty($venta->cliente->telef1))
            <tr>
                <td class="label">Teléfono:</td>
                <td>{{ $venta->cliente->telef1 }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Vendedor:</td>
                <td>{{ $venta->vendedorPorUsuario->nombre ?? 'N/A' }} ({{ $venta->vendedorPorUsuario->clave ?? 'N/A' }})</td>
            </tr>
        </table>
    </div>

    <!-- LISTADO DE PRODUCTOS -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 20%;">CANT</th>
                <th style="width: 50%;">DESCRIPCIÓN</th>
                <th style="width: 30%;" class="text-right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->productos as $item)
                @php
                    $cantidad = (float) $item->cantidad;
                    $precio = (float) $item->precio_unitario;
                    $subtotalProd = $cantidad * $precio;
                @endphp
                <tr>
                    <td style="font-weight: bold;">
                        {{ number_format($cantidad, 2) }}
                        <span style="font-size: 8px; font-weight: normal;">{{ $item->id_unidad_medida }}</span>
                    </td>
                    <td>
                        <span class="item-name">{{ $item->producto->item_name ?? 'Producto' }}</span>
                        <span class="item-subtext">{{ number_format($cantidad, 2) }} x ${{ number_format($precio, 2) }}</span>
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        ${{ number_format($subtotalProd, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- CÁLCULOS Y TOTALES -->
    @php
        $importeTotal = (float) $venta->importe;
        $porcentajeIva = (float) $venta->iva_aplicado;
        $descuento = (float) $venta->descuento;
        
        // Cálculo inverso para desglosar IVA si está incluido en el importe total
        $subtotal = $porcentajeIva > 0 ? ($importeTotal / (1 + ($porcentajeIva / 100))) : $importeTotal;
        $montoIva = $importeTotal - $subtotal;
    @endphp

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">${{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($porcentajeIva > 0)
            <tr>
                <td>IVA ({{ number_format($porcentajeIva, 0) }}%):</td>
                <td class="text-right">${{ number_format($montoIva, 2) }}</td>
            </tr>
            @endif
            @if($descuento > 0)
            <tr>
                <td>Descuento:</td>
                <td class="text-right">-${{ number_format($descuento, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>TOTAL:</td>
                <td class="text-right">${{ number_format($importeTotal, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- PIE DEL TICKET -->
    <div class="footer">
        <div style="font-size: 11px; font-weight: bold; margin-bottom: 4px;">¡GRACIAS POR SU COMPRA!</div>
        <div>Conserve este ticket para cualquier aclaración o garantía.</div>
        
        <div style="margin-top: 10px; font-size: 8.5px; font-weight: bold;">
            *{{ $venta->folio_venta }}*
        </div>
        
        <div style="margin-top: 6px; font-size: 8px; color: #777777;">
            Impreso el {{ now()->format('d/m/Y h:i A') }}
        </div>
    </div>
</div>

</body>
</html>