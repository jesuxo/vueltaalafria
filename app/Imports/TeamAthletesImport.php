<?php
// app/Imports/TeamAthletesImport.php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\TeamStaff;
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
    private $staffCount = 0;
    private $athleteCount = 0;

    private $validCategories = [
        'COMPOTAS', 'INICIACIÓN A', 'INICIACIÓN B', 'INICIACIÓN C',
        'PRE-INFANTIL', 'INFANTIL', 'PRE-JUVENIL', 'JUVENIL', 'STAFF'
    ];

    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    public function model(array $row)
    {
        // Limpiar datos
        $nombres = trim($row['nombres'] ?? '');
        $apellidos = trim($row['apellidos'] ?? '');
        $categoria = trim($row['categoria'] ?? '');
        $rol = trim($row['rol'] ?? 'Atleta');
        $tipoDocumento = trim($row['tipo_documento'] ?? '');
        $numeroDocumento = trim($row['numero_documento'] ?? '');
        $uciId = trim($row['uci_id'] ?? '');
        $genero = trim($row['genero'] ?? '');

        // Validar datos mínimos
        if (empty($nombres) && empty($apellidos)) {
            $this->errors[] = "Fila con ID {$row['id']}: Nombres y Apellidos vacíos";
            return null;
        }

        // Si es STAFF
        if ($categoria === 'STAFF' || $rol === 'STAFF') {
            $this->staffCount++;

            // Crear registro en team_staff
            TeamStaff::create([
                'team_id' => $this->teamId,
                'full_name' => strtoupper($nombres . ' ' . $apellidos),
                'identification_number' => $numeroDocumento,
                'role' => 'STAFF',
                'phone' => null,
                'email' => null,
                'position' => $row['cargo'] ?? 'Personal de apoyo',
                'is_active' => true
            ]);

            return null; // No crear atleta para STAFF
        }

        // Validar categoría para atletas
        if (!in_array($categoria, $this->validCategories)) {
            $this->errors[] = "Fila {$row['id']}: Categoría '{$categoria}' no válida";
            return null;
        }

        // Validar género
        if (!in_array($genero, ['Masculino', 'Femenino'])) {
            $this->errors[] = "Fila {$row['id']}: Género '{$genero}' no válido. Use Masculino o Femenino";
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
            if (!$this->validateAgeByCategory($categoria, $age)) {
                $this->errors[] = "Fila {$row['id']}: Edad {$age} años no corresponde a la categoría {$categoria}";
                return null;
            }

        } catch (\Exception $e) {
            $this->errors[] = "Fila {$row['id']}: Error en fecha de nacimiento";
            return null;
        }

        // Generar dorsal único
        $dorsalNumber = $this->generateDorsalNumber();

        $this->athleteCount++;
        $this->importedCount++;

        // Determinar el tipo de documento para menores
        $documentType = $tipoDocumento;
        $documentNumber = $numeroDocumento;

        // Si es menor de 9 años y el tipo de documento es especial
        if ($age < 9 && $tipoDocumento === 'CEDULA_REPRESENTANTE') {
            $documentType = 'CEDULA_REPRESENTANTE';
            // Nota: el número de documento sería la cédula del representante
        }

        return new Athlete([
            'first_name' => strtoupper($nombres),
            'last_name' => strtoupper($apellidos),
            'dorsal_number' => $dorsalNumber,
            'team_id' => $this->teamId,
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'uci_id' => $uciId,
            'gender' => $genero,
            'category' => $categoria,
            'birth_date' => $birthDate,
            'nationality' => $row['nacionalidad'] ?? 'Venezolana',
            'is_active' => true
        ]);
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required',
            '*.nombres' => 'required_without:*.apellidos',
            '*.apellidos' => 'required_without:*.nombres',
            '*.categoria' => 'required|string',
            '*.fecha_de_nacimiento' => 'required_if:*.categoria,!=,STAFF'
        ];
    }

    private function parseDate($dateString)
    {
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd.m.Y', 'd/m/y', 'd-m-y'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                if ($date && $date->year > 1900 && $date->year <= date('Y')) {
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
            'INICIACIÓN C' => [9, 10],
            'PRE-INFANTIL' => [11, 12],
            'INFANTIL' => [13, 14],
            'PRE-JUVENIL' => [15, 16],
            'JUVENIL' => [17, 18],
            'STAFF' => [2, 99]
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

    public function getStaffCount()
    {
        return $this->staffCount;
    }

    public function getAthleteCount()
    {
        return $this->athleteCount;
    }
}
