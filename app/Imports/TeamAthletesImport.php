<?php
// app/Imports/TeamAthletesImport.php

namespace App\Imports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Carbon\Carbon;

class TeamAthletesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    private $teamId;
    private $errors = [];
    private $importedCount = 0;

    // Mapeo de categorías
    private $categoryMapping = [
        'COMPOTAS' => 'COMPOTAS',
        'INICIACIÓN A' => 'INICIACIÓN A',
        'INICIACIÓN B' => 'INICIACIÓN B',
        'EXHIBICIÓN' => 'EXHIBICIÓN',
        'PRE-INFANTIL D' => 'PRE-INFANTIL D',
        'INFANTIL' => 'INFANTIL',
        'PRE-JUVENIL' => 'PRE-JUVENIL',
        'JUVENIL' => 'JUVENIL'
    ];

    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    public function model(array $row)
    {
        // Validar categoría
        $category = $row['categoria'] ?? null;
        if (!array_key_exists($category, $this->categoryMapping)) {
            $this->errors[] = "Fila {$row['id']}: Categoría '{$category}' no válida";
            return null;
        }

        // Validar género
        $gender = $row['genero'] ?? null;
        if (!in_array($gender, ['Masculino', 'Femenino'])) {
            $this->errors[] = "Fila {$row['id']}: Género '{$gender}' no válido. Use Masculino o Femenino";
            return null;
        }

        // Convertir fecha
        try {
            $birthDate = $this->parseDate($row['fecha_de_nacimiento']);
            if (!$birthDate) {
                $this->errors[] = "Fila {$row['id']}: Formato de fecha inválido. Use dd/mm/aaaa";
                return null;
            }

            // Validar edad según categoría
            $age = Carbon::parse($birthDate)->age;
            if (!$this->validateAgeByCategory($category, $age)) {
                $this->errors[] = "Fila {$row['id']}: Edad {$age} años no corresponde a la categoría {$category}";
                return null;
            }

        } catch (\Exception $e) {
            $this->errors[] = "Fila {$row['id']}: Error en fecha de nacimiento";
            return null;
        }

        // Generar dorsal único
        $dorsalNumber = $this->generateDorsalNumber();

        $this->importedCount++;

        return new Athlete([
            'first_name' => strtoupper($row['nombres']),
            'last_name' => strtoupper($row['apellidos']),
            'dorsal_number' => $dorsalNumber,
            'team_id' => $this->teamId,
            'document_type' => $row['tipo_documento'] ?? null,
            'document_number' => $row['numero_documento'] ?? null,
            'uci_id' => $row['uci_id'] ?? null,
            'gender' => $gender,
            'category' => $category,
            'birth_date' => $birthDate,
            'nationality' => $row['nacionalidad'] ?? 'Venezolana',
            'is_active' => true
        ]);
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required|integer',
            '*.apellidos' => 'required|string|max:255',
            '*.nombres' => 'required|string|max:255',
            '*.fecha_de_nacimiento' => 'required',
            '*.categoria' => 'required|string',
            '*.genero' => 'required|string'
        ];
    }

    private function parseDate($dateString)
    {
        // Intentar varios formatos
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd.m.Y'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    private function validateAgeByCategory($category, $age)
    {
        $ageRanges = [
            'COMPOTAS' => [3, 4],
            'INICIACIÓN A' => [5, 6],
            'INICIACIÓN B' => [7, 8],
            'EXHIBICIÓN' => [9, 10],
            'PRE-INFANTIL D' => [11, 12],
            'INFANTIL' => [13, 14],
            'PRE-JUVENIL' => [15, 16],
            'JUVENIL' => [17, 18]
        ];

        if (!isset($ageRanges[$category])) {
            return false;
        }

        $range = $ageRanges[$category];
        return $age >= $range[0] && $age <= $range[1];
    }

    private function generateDorsalNumber()
    {
        $lastAthlete = Athlete::orderBy('id', 'desc')->first();
        $number = $lastAthlete ? intval(substr($lastAthlete->dorsal_number, 1)) + 1 : 1;
        return 'D' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }
}
