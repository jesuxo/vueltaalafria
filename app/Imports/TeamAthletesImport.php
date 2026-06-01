<?php
// app/Imports/TeamAthletesImport.php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\TeamStaff;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Throwable;

class TeamAthletesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use Importable;

    private $teamId;
    private $errors = [];
    private $importedCount = 0;
    private $staffCount = 0;
    private $athleteCount = 0;
    private $currentRow = 0;

    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * Manejar errores individuales (requerido por SkipsOnError)
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = 'Error en fila ' . $this->currentRow . ': ' . $e->getMessage();
    }

    /**
     * Calcular categoría SOLO por edad y género
     */
    private function calculateCategoryByAge($birthDate, $gender)
    {
        $age = Carbon::parse($birthDate)->age;

        // Mapeo de edad a categoría
        if ($age >= 3 && $age <= 4) {
            return 'Compota';
        } elseif ($age >= 5 && $age <= 6) {
            return 'Iniciación A';
        } elseif ($age >= 7 && $age <= 8) {
            return 'Iniciación B';
        } elseif ($age >= 9 && $age <= 10) {
            return 'Iniciación C';
        } elseif ($age >= 11 && $age <= 12) {
            return $gender === 'Masculino' ? 'Pre-Infantil Masculino' : 'Pre-Infantil Femenino';
        } elseif ($age >= 13 && $age <= 14) {
            return $gender === 'Masculino' ? 'Infantil Masculino' : 'Infantil Femenino';
        } elseif ($age >= 15 && $age <= 16) {
            return $gender === 'Masculino' ? 'Pre-Juvenil Masculino' : 'Pre-Juvenil Femenino';
        } elseif ($age >= 17 && $age <= 18) {
            return $gender === 'Masculino' ? 'Juvenil Masculino' : 'Juvenil Femenino';
        }

        return null;
    }

    public function model(array $row)
    {
        $this->currentRow++;

        // Limpiar datos
        $id = trim($row['id'] ?? '');
        $nombres = trim($row['nombres'] ?? '');
        $apellidos = trim($row['apellidos'] ?? '');
        $rol = trim($row['rol'] ?? 'Atleta');
        $tipoDocumento = trim($row['tipo_documento'] ?? '');
        $numeroDocumento = trim($row['numero_documento'] ?? '');
        $uciId = trim($row['uci_id'] ?? '');
        $genero = trim($row['genero'] ?? '');

        // Ignorar filas completamente vacías
        if (empty($nombres) && empty($apellidos)) {
            return null;
        }

        // Validar datos mínimos
        if (empty($nombres)) {
            $this->errors[] = "Fila {$this->currentRow}: El campo NOMBRES es obligatorio";
            return null;
        }
        if (empty($apellidos)) {
            $this->errors[] = "Fila {$this->currentRow}: El campo APELLIDOS es obligatorio";
            return null;
        }

        // Procesar STAFF
        if (strtoupper($rol) === 'STAFF') {
            $this->staffCount++;

            TeamStaff::create([
                'team_id' => $this->teamId,
                'full_name' => strtoupper($nombres . ' ' . $apellidos),
                'identification_number' => $numeroDocumento,
                'role' => 'STAFF',
                'phone' => null,
                'email' => null,
                'position' => 'Personal de apoyo',
                'is_active' => true
            ]);

            return null;
        }

        // Validar género
        $generoNormalizado = ucfirst(strtolower($genero));
        if (!in_array($generoNormalizado, ['Masculino', 'Femenino'])) {
            $this->errors[] = "Fila {$this->currentRow}: Género '{$genero}' no válido. Use Masculino o Femenino";
            return null;
        }

        // Validar fecha de nacimiento
        $fechaNacimiento = $row['fecha_de_nacimiento'] ?? null;
        if (empty($fechaNacimiento)) {
            $this->errors[] = "Fila {$this->currentRow}: La fecha de nacimiento es obligatoria";
            return null;
        }

        $birthDate = $this->parseDate($fechaNacimiento);
        if (!$birthDate) {
            $this->errors[] = "Fila {$this->currentRow}: Formato de fecha inválido. Use dd/mm/aaaa";
            return null;
        }

        $age = Carbon::parse($birthDate)->age;

        if ($age < 3) {
            $this->errors[] = "Fila {$this->currentRow}: Edad {$age} años. Edad mínima permitida: 3 años";
            return null;
        }
        if ($age > 18) {
            $this->errors[] = "Fila {$this->currentRow}: Edad {$age} años. Edad máxima permitida: 18 años";
            return null;
        }

        // Calcular categoría automáticamente
        $categoriaCalculada = $this->calculateCategoryByAge($birthDate, $generoNormalizado);
        if (!$categoriaCalculada) {
            $this->errors[] = "Fila {$this->currentRow}: No se pudo determinar categoría para edad {$age} años";
            return null;
        }

        // Generar dorsal único
        $dorsalNumber = $this->generateDorsalNumber();
        $this->athleteCount++;
        $this->importedCount++;

        return new Athlete([
            'first_name' => strtoupper($nombres),
            'last_name' => strtoupper($apellidos),
            'dorsal_number' => $dorsalNumber,
            'team_id' => $this->teamId,
            'document_type' => $tipoDocumento ?: 'NO ESPECIFICADO',
            'document_number' => $numeroDocumento,
            'uci_id' => $uciId,
            'gender' => $generoNormalizado,
            'category' => $categoriaCalculada,
            'birth_date' => $birthDate,
            'nationality' => $row['nacionalidad'] ?? 'Venezolana',
            'is_active' => true
        ]);
    }

    public function rules(): array
    {
        return [
            '*.fecha_de_nacimiento' => 'required_if:*.rol,!=,STAFF'
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    public function customValidationMessages()
    {
        return [
            'fecha_de_nacimiento.required_if' => 'La fecha de nacimiento es obligatoria para los atletas',
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
