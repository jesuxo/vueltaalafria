<?php
// app/Exports/PhotoRevenueExport.php

namespace App\Exports;

use App\Models\PhotoOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class PhotoRevenueExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $orders;
    protected $summary;

    public function __construct($orders, $summary = null)
    {
        $this->orders = $orders;
        $this->summary = $summary;
    }

    /**
     * Colección de datos a exportar
     */
    public function collection()
    {
        return $this->orders;
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'N° Pedido',
            'Código Público',
            'Cliente',
            'Email',
            'Teléfono',
            'Cantidad Fotos',
            'Subtotal (USD)',
            'Total (USD)',
            'Método de Pago',
            'Referencia',
            'Estado',
            'Fecha de Creación',
            'Fecha de Pago',
            'Fecha de Entrega'
        ];
    }

    /**
     * Mapeo de los datos
     */
    public function map($order): array
    {
        return [
            $order->order_number,
            $order->public_code,
            $order->customer_name,
            $order->customer_email ?? 'N/A',
            $order->customer_phone,
            $order->items->count(),
            number_format($order->subtotal, 2),
            number_format($order->total, 2),
            $this->getPaymentMethodName($order->payment_method),
            $order->payment_reference ?? 'N/A',
            $this->getStatusName($order->status),
            $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A',
            $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : 'N/A',
            $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i') : 'N/A',
        ];
    }

    /**
     * Estilos del Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para el encabezado
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00ECFE']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Centrar algunas columnas
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('K:M')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Autoajustar altura de filas
        $sheet->getStyle('A1:N' . ($this->orders->count() + 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Colores por estado
        $lastRow = $this->orders->count() + 1;
        for ($i = 2; $i <= $lastRow; $i++) {
            $status = $sheet->getCell('K' . $i)->getValue();
            if ($status == 'Completado') {
                $sheet->getStyle('K' . $i)->getFont()->getColor()->setRGB('28a745');
            } elseif ($status == 'Pagado') {
                $sheet->getStyle('K' . $i)->getFont()->getColor()->setRGB('17a2b8');
            } elseif ($status == 'Pendiente') {
                $sheet->getStyle('K' . $i)->getFont()->getColor()->setRGB('ffc107');
            }
        }

        // Agregar resumen si existe
        if ($this->summary) {
            $summaryRow = $lastRow + 2;
            $sheet->setCellValue('A' . $summaryRow, 'RESUMEN GENERAL');
            $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(12);

            $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Ingresos:');
            $sheet->setCellValue('B' . ($summaryRow + 1), '$' . number_format($this->summary['total_revenue'], 2));
            $sheet->getStyle('B' . ($summaryRow + 1))->getFont()->setBold(true);

            $sheet->setCellValue('A' . ($summaryRow + 2), 'Total Pedidos:');
            $sheet->setCellValue('B' . ($summaryRow + 2), $this->summary['total_orders']);

            $sheet->setCellValue('A' . ($summaryRow + 3), 'Total Fotos Vendidas:');
            $sheet->setCellValue('B' . ($summaryRow + 3), $this->summary['total_photos_sold']);

            $sheet->setCellValue('A' . ($summaryRow + 4), 'Valor Promedio por Pedido:');
            $sheet->setCellValue('B' . ($summaryRow + 4), '$' . number_format($this->summary['avg_order_value'], 2));

            $sheet->getStyle('A' . ($summaryRow + 1) . ':B' . ($summaryRow + 4))->getFont()->setSize(11);
        }

        return [];
    }

    /**
     * Anchos de las columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // N° Pedido
            'B' => 18,  // Código Público
            'C' => 25,  // Cliente
            'D' => 30,  // Email
            'E' => 15,  // Teléfono
            'F' => 12,  // Cantidad Fotos
            'G' => 15,  // Subtotal
            'H' => 15,  // Total
            'I' => 18,  // Método de Pago
            'J' => 20,  // Referencia
            'K' => 15,  // Estado
            'L' => 18,  // Fecha Creación
            'M' => 18,  // Fecha Pago
            'N' => 18,  // Fecha Entrega
        ];
    }

    /**
     * Obtener nombre del método de pago
     */
    private function getPaymentMethodName($method)
    {
        switch ($method) {
            case 'transferencia':
                return 'Transferencia Bancaria';
            case 'bancolombia':
                return 'Bancolombia (COP)';
            case 'usdt':
                return 'USDT (Cripto)';
            default:
                return ucfirst($method);
        }
    }

    /**
     * Obtener nombre del estado
     */
    private function getStatusName($status)
    {
        switch ($status) {
            case 'pending':
                return 'Pendiente';
            case 'paid':
                return 'Pagado';
            case 'processing':
                return 'Procesando';
            case 'completed':
                return 'Completado';
            case 'cancelled':
                return 'Cancelado';
            default:
                return ucfirst($status);
        }
    }
}
