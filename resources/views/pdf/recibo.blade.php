<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Courier', monospace; font-size: 12px; width: 100%; margin: 0; }
        .header { text-align: center; margin-bottom: 10px; }
        .linea { border-top: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        .text-right { text-align: right; }
        .total { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <strong>MOTO STOCK.</strong><br>
        San Miguel, El Salvador<br>
        NIT: 0000-000000-000-0
    </div>

    <div class="linea"></div>
    <p>
        Ticket: #{{ $recibo->id }}<br>
        Fecha: {{ $recibo->fecha }}<br>
        Cliente: {{ $recibo->cliente ? $recibo->cliente->nombres_cliente : 'Público General' }}<br>
        @if($recibo->email_invitado) Email: {{ $recibo->email_invitado }} @endif
    </p>
    <div class="linea"></div>

    <table>
        <thead>
            <tr>
                <th align="left">Cant.</th>
                <th align="left">Prod.</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recibo->productos as $p)
            <tr>
                <td style="width: 15%;">{{ $p->pivot->cantidad }}</td>
                <td style="width: 50%;">{{ $p->nombre_producto }}</td>
                <td style="width: 15%;" class="text-right">
                    ${{ number_format($p->pivot->precio_unitario, 2) }}
                </td>
                <td style="width: 20%;" class="text-right">
                    ${{ number_format($p->pivot->cantidad * $p->pivot->precio_unitario, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="linea"></div>
    <div class="text-right total">
        TOTAL: ${{ number_format($recibo->total, 2) }}
    </div>
    <div class="linea"></div>

    <div class="header" style="margin-top: 15px;">
        ¡GRACIAS POR SU COMPRA!
    </div>
</body>
</html>