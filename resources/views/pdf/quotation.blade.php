<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $quotation->folio }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; font-size: 12px; }
        .header { border-bottom: 2px solid #222; margin-bottom: 20px; padding-bottom: 12px; }
        .header table, .summary, .items { width: 100%; border-collapse: collapse; }
        .header td:last-child { text-align: right; }
        h1 { margin: 0 0 6px; font-size: 24px; }
        .muted { color: #666; }
        .summary { margin-bottom: 20px; }
        .summary td { width: 50%; padding: 4px 0; vertical-align: top; }
        .items th { background: #eeeeee; text-align: left; }
        .items th, .items td { border: 1px solid #cccccc; padding: 7px; }
        .number { text-align: right; }
        .totals { margin-top: 16px; margin-left: auto; width: 280px; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 4px; }
        .total { border-top: 2px solid #222; font-size: 15px; font-weight: bold; }
        .notes { margin-top: 26px; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <h1>Cotización</h1>
                    <div class="muted">Folio: {{ $quotation->folio }}</div>
                </td>
                <td>
                    <strong>Fecha:</strong> {{ $quotation->issued_at?->format('d/m/Y') }}<br>
                    @if ($quotation->expires_at)
                        <strong>Vigencia:</strong> {{ $quotation->expires_at->format('d/m/Y') }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="summary">
        <tr>
            <td>
                <strong>Cliente</strong><br>
                {{ $quotation->client->nombre_comercial ?? $quotation->client->razon_social ?? 'Cliente no disponible' }}<br>
                <span class="muted">RFC: {{ $quotation->client->rfc ?? 'N/D' }}</span>
            </td>
            <td>
                <strong>Contacto</strong><br>
                @if ($quotation->contact)
                    {{ $quotation->contact->first_name }} {{ $quotation->contact->father_last_name }}<br>
                    {{ $quotation->contact->email ?? '' }}
                @else
                    Sin contacto asignado
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="number">Cantidad</th>
                <th class="number">Precio unitario</th>
                <th class="number">Descuento</th>
                <th class="number">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="number">{{ number_format($item->quantity, 2) }}</td>
                    <td class="number">{{ $quotation->currency }} {{ number_format($item->unit_price, 2) }}</td>
                    <td class="number">{{ number_format($item->discount_percentage, 2) }}%</td>
                    <td class="number">{{ $quotation->currency }} {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr><td>Subtotal:</td><td class="number">{{ $quotation->currency }} {{ number_format($quotation->subtotal, 2) }}</td></tr>
            <tr><td>Descuento:</td><td class="number">{{ $quotation->currency }} {{ number_format($quotation->discount, 2) }}</td></tr>
            <tr><td>Impuestos:</td><td class="number">{{ $quotation->currency }} {{ number_format($quotation->tax, 2) }}</td></tr>
            <tr class="total"><td>Total:</td><td class="number">{{ $quotation->currency }} {{ number_format($quotation->total, 2) }}</td></tr>
        </table>
    </div>

    @if ($quotation->notes || $quotation->terms_conditions)
        <div class="notes">
            @if ($quotation->notes)
                <strong>Notas</strong><br>{{ $quotation->notes }}<br><br>
            @endif
            @if ($quotation->terms_conditions)
                <strong>Términos y condiciones</strong><br>{{ $quotation->terms_conditions }}
            @endif
        </div>
    @endif
</body>
</html>
