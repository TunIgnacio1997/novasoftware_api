<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>

        body{
            font-family: Arial, sans-serif;
            margin: 10px;
        }

        table{
            width: 100%;
            border-collapse: collapse;
        }

        td{
            width: 33%;
            padding: 8px;
        }

        .card{
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            text-align: center;
            padding: 8px;
            height: 100px;
        }

        .title{
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 6px;
            height: 15px;
        }

        .barcode{
            margin-top: 4px;
        }

        .barcode img{
            width: 180px;
            height: 35px;
        }

        .code{
            font-size: 10px;
            margin-top: 4px;
        }

    </style>

</head>

<body>

<table>

    <tr>

    @foreach($items as $index => $item)

        <td>

            <div class="card">

                <div class="title">
                    {{ $item['nombre'] }}
                </div>

                <div class="barcode">
                    <img src="data:image/png;base64,{{ $item['barcode'] }}">
                </div>

                <div class="code">
                    {{ $item['codigo'] }}
                </div>

            </div>

        </td>

        @if(($index + 1) % 3 == 0)
            </tr><tr>
        @endif

    @endforeach

    </tr>

</table>

</body>
</html>