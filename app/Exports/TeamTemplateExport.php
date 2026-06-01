<?php
// app/Exports/TeamTemplateExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TeamTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            // Ejemplo de atleta
            [
                '1',
                'JUAN',
                'PEREZ',
                '15/05/2010',
                'PASAPORTE',
                'ABC123456',
                'UCI123456',
                'JUVENIL',
                'Masculino',
                'Atleta'
            ],
            // Ejemplo de staff
            [
                '2',
                'MARIA',
                'RODRIGUEZ',
                '20/08/1985',
                'CEDULA',
                '12345678',
                '',
                'STAFF',
                'Femenino',
                'STAFF'
            ],
            // Ejemplo de staff adicional
            [
                '3',
                'CARLOS',
                'GONZALEZ',
                '10/03/1990',
                'PASAPORTE',
                'DEF789012',
                '',
                'STAFF',
                'Masculino',
                'STAFF'
            ],
            // Filas vacías para que el usuario llene
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            // Notas explicativas
            ['NOTAS IMPORTANTES:', '', '', '', '', '', '', '', '', ''],
            ['1. Para menores de 9 años, en TIPO_DOCUMENTO usar "CEDULA_REPRESENTANTE" y en NUMERO_DOCUMENTO colocar la cédula del padre/madre', '', '', '', '', '', '', '', '', ''],
            ['2. CATEGORIAS válidas: COMPOTAS, INICIACIÓN A, INICIACIÓN B, INICIACIÓN C, PRE-INFANTIL, INFANTIL, PRE-JUVENIL, JUVENIL, STAFF', '', '', '', '', '', '', '', '', ''],
            ['1. Para STAFF, la categoría debe ser "STAFF" y el rol "STAFF"', '', '', '', '', '', '', '', '', ''],

        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'NOMBRES',
            'APELLIDOS',
            'FECHA_DE_NACIMIENTO (dd/mm/aaaa)',
            'TIPO_DOCUMENTO (CEDULA/PASAPORTE)',
            'NUMERO_DOCUMENTO',
            'UCI_ID',
            'CATEGORIA',
            'GENERO (Masculino/Femenino)',
            'ROL (Atleta/STAFF)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para los encabezados
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
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

        // Estilo para las notas
        $sheet->getStyle('A13:A18')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FF0000'],
                'size' => 10
            ]
        ]);

        // Color de fondo para filas de ejemplo
        $sheet->getStyle('A2:J4')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F0FE']
            ]
        ]);

        // Alto de filas
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
