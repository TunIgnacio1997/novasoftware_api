<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <style type="text/css">
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.invoice-container {
    width: auto;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.logo img {
    width: 150px;
}

.company-details {
    text-align: right;
}

.invoice-details h3 {
    margin-bottom: 10px;
}

.invoice-details p {
    margin: 5px 0;
}

.invoice-items table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.invoice-items th, .invoice-items td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.invoice-items th {
    background-color: #f4f4f4;
}

.invoice-total {
    text-align: right;
    font-size: 1.2em;
    font-weight: bold;
}

    </style>
    <div class="invoice-container">
        <header class="invoice-header">
            <div class="logo">
                <img src="{{ $logo }}" alt="Logo de la Empresa" width="200">
            </div>
            <div class="company-details">
                <h2>{{ $nombreEmpresa }}</h2>
                <p>{{$direccionEmpresa}}</p>
                <p>Teléfono: 123-456-789</p>
                <p>Email: {{$correoEmpresa}}</p>
            </div>
        </header>

        <section class="invoice-details">
            <h3>Compra</h3>
            <p><strong>Número de compra:</strong> {{$invoice_number}}</p>
            <p><strong>Fecha de compra:</strong> {{ $date }}</p>
            <p><strong>Fecha de recepcion:</strong> {{ $f_recepcion }}</p>
            <p><strong>Proveedor:</strong> {{ $client_name }}</p>
            <p></p>
        </section>

        <section class="invoice-items">
            <table>
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['producto']['item_name'] }}</td>
                            <td>{{ $item['cantidad'] }}</td>
                            <td>{{ number_format($item['precio_unitario'], 2, '.', ',') }}</td>
                            <td>{{ number_format($item['cantidad'] * $item['producto']['buy_price'] , 2, '.', ',') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="invoice-total">
            <p><strong>Total:</strong> ${{$total}}</p>
        </section>
    </div>
</body>
</html>
