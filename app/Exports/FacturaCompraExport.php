<?php

namespace App\Exports;

use App\Models\FacturaCompra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FacturaCompraExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    public function __construct(private FacturaCompra $factura) {}

    public function collection()
    {
        return $this->factura->detalles()->with(['producto', 'marca'])->get();
    }

    public function headings(): array
    {
        return [
            'Producto',
            'Marca',
            'Cant. Esperada',
            'Cant. Recibida',
            'Precio Unitario ($)',
            'Subtotal ($)',
            'Estado Línea',
        ];
    }

    public function map($detalle): array
    {
        $completo = $detalle->cantidad_recibida >= $detalle->cantidad_esperada;
        $parcial  = $detalle->cantidad_recibida > 0 && !$completo;

        return [
            $detalle->producto->nombre_producto ?? '—',
            $detalle->marca->nombre_marca ?? '—',
            $detalle->cantidad_esperada,
            $detalle->cantidad_recibida,
            number_format((float) $detalle->precio_unitario, 2, '.', ''),
            number_format((float) $detalle->precio_unitario * $detalle->cantidad_esperada, 2, '.', ''),
            $completo ? 'Completo' : ($parcial ? 'Parcial' : 'Pendiente'),
        ];
    }

    public function title(): string
    {
        return 'Detalle';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Encabezados en negrita con fondo gris oscuro
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF374151']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        $factura = $this->factura;

        return [
            AfterSheet::class => function (AfterSheet $event) use ($factura) {
                $sheet = $event->sheet->getDelegate();

                // Insertar 5 filas al inicio para el encabezado de la factura
                $sheet->insertNewRowBefore(1, 5);

                $estadoLabel = match ($factura->estado) {
                    'recibida' => 'Recibida',
                    'parcial'  => 'Parcial',
                    default    => 'Pendiente',
                };

                // Fila 1: título
                $sheet->setCellValue('A1', 'FACTURA DE COMPRA');
                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Fila 2: número de factura
                $sheet->setCellValue('A2', 'N° Factura:');
                $sheet->setCellValue('B2', $factura->numero_factura);
                $sheet->getStyle('A2')->getFont()->setBold(true);

                // Fila 3: fecha
                $sheet->setCellValue('A3', 'Fecha:');
                $sheet->setCellValue('B3', $factura->fecha->format('d/m/Y'));
                $sheet->getStyle('A3')->getFont()->setBold(true);

                // Fila 4: proveedor
                $sheet->setCellValue('A4', 'Proveedor:');
                $sheet->setCellValue('B4', $factura->proveedor->nombre_proveedor ?? '—');
                $sheet->getStyle('A4')->getFont()->setBold(true);

                // Fila 5: estado
                $sheet->setCellValue('A5', 'Estado:');
                $sheet->setCellValue('B5', $estadoLabel);
                $sheet->getStyle('A5')->getFont()->setBold(true);

                // Bordes en la tabla de detalles (fila 6 = encabezados, desde fila 7 = datos)
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 6) {
                    $sheet->getStyle("A6:G{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFD1D5DB'],
                            ],
                        ],
                    ]);
                }

                // Fila de total
                $totalRow = $lastRow + 1;
                $sheet->setCellValue("E{$totalRow}", 'TOTAL:');
                $sheet->setCellValue("F{$totalRow}", '=SUM(F7:F' . $lastRow . ')');
                $sheet->getStyle("E{$totalRow}:F{$totalRow}")->getFont()->setBold(true);
                $sheet->getStyle("F{$totalRow}")->getNumberFormat()->setFormatCode('"$"#,##0.00');

                // Formato numérico para columnas de precios
                $sheet->getStyle("E7:F{$lastRow}")->getNumberFormat()->setFormatCode('"$"#,##0.00');

                // Alineación centrada para cantidades
                $sheet->getStyle("C7:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
