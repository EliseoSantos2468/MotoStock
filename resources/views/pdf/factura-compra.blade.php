<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; background: #fff; padding: 30px; }

        .header { margin-bottom: 20px; border-bottom: 2px solid #374151; padding-bottom: 14px; }
        .header h1 { font-size: 20px; font-weight: bold; color: #111827; margin-bottom: 2px; }
        .header .subtitle { font-size: 11px; color: #6b7280; }

        .meta-grid { display: table; width: 100%; margin-bottom: 18px; }
        .meta-col { display: table-cell; width: 50%; vertical-align: top; }
        .meta-item { margin-bottom: 5px; }
        .meta-label { font-weight: bold; color: #374151; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
        .badge-pendiente { background: #fef3c7; color: #92400e; }
        .badge-parcial    { background: #dbeafe; color: #1e40af; }
        .badge-recibida   { background: #d1fae5; color: #065f46; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        thead tr { background: #374151; color: #ffffff; }
        thead th { padding: 7px 8px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; }
        thead th.text-center { text-align: center; }
        thead th.text-right  { text-align: right; }

        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tbody td.text-center { text-align: center; }
        tbody td.text-right  { text-align: right; }

        .status-completo  { color: #059669; font-weight: bold; }
        .status-parcial   { color: #2563eb; font-weight: bold; }
        .status-pendiente { color: #9ca3af; }

        tfoot td { padding: 8px; font-weight: bold; border-top: 2px solid #374151; }

        .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Factura de Compra</h1>
        <div class="subtitle">Recepción de Mercancía — MotoStock</div>
    </div>

    <div class="meta-grid">
        <div class="meta-col">
            <div class="meta-item">
                <span class="meta-label">N° Factura:</span>
                {{ $factura->numero_factura }}
            </div>
            <div class="meta-item">
                <span class="meta-label">Fecha:</span>
                {{ $factura->fecha->format('d/m/Y') }}
            </div>
        </div>
        <div class="meta-col">
            <div class="meta-item">
                <span class="meta-label">Proveedor:</span>
                {{ $factura->proveedor->nombre_proveedor ?? '—' }}
                @if($factura->proveedor?->telefono)
                    — Tel: {{ $factura->proveedor->telefono }}
                @endif
            </div>
            <div class="meta-item">
                <span class="meta-label">Estado:</span>
                @php
                    $badgeClass = match($factura->estado) {
                        'recibida' => 'badge-recibida',
                        'parcial'  => 'badge-parcial',
                        default    => 'badge-pendiente',
                    };
                    $badgeLabel = match($factura->estado) {
                        'recibida' => 'Recibida',
                        'parcial'  => 'Parcial',
                        default    => 'Pendiente',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Marca</th>
                <th class="text-center">Cant. Esperada</th>
                <th class="text-center">Cant. Recibida</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-right">Subtotal</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($factura->detalles as $detalle)
                @php
                    $completo = $detalle->cantidad_recibida >= $detalle->cantidad_esperada;
                    $parcial  = $detalle->cantidad_recibida > 0 && !$completo;
                @endphp
                <tr>
                    <td>{{ $detalle->producto->nombre_producto ?? '—' }}</td>
                    <td>{{ $detalle->marca->nombre_marca ?? '—' }}</td>
                    <td class="text-center">{{ $detalle->cantidad_esperada }}</td>
                    <td class="text-center">{{ $detalle->cantidad_recibida }}</td>
                    <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-right">${{ number_format($detalle->precio_unitario * $detalle->cantidad_esperada, 2) }}</td>
                    <td class="text-center">
                        @if ($completo)
                            <span class="status-completo">Completo</span>
                        @elseif ($parcial)
                            <span class="status-parcial">Parcial</span>
                        @else
                            <span class="status-pendiente">Pendiente</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right;">TOTAL:</td>
                <td style="text-align:right;">
                    ${{ number_format($factura->detalles->sum(fn($d) => $d->precio_unitario * $d->cantidad_esperada), 2) }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} — MotoStock
    </div>

</body>
</html>
