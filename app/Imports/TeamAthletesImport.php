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
    private $athletesList = [];

    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = 'Error en fila ' . $this->currentRow . ': ' . $e->getMessage();
    }

    private function getValueFromRow($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && !empty(trim($row[$key]))) {
                return trim($row[$key]);
            }
            foreach ($row as $rowKey => $rowValue) {
                if (strtolower(trim($rowKey)) === strtolower($key)) {
                    return trim($rowValue);
                }
            }
            foreach ($row as $rowKey => $rowValue) {
                if (strpos(strtolower($rowKey), strtolower($key)) !== false) {
                    return trim($rowValue);
                }
            }
        }
        return '';
    }

    private function calculateCategoryByAge($birthDate, $gender)
    {
        $age = Carbon::parse($birthDate)->age;

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

    private function isRowEmpty($row)
    {
        $nombres = trim($row['NOMBRES'] ?? $row['nombres'] ?? '');
        $apellidos = trim($row['APELLIDOS'] ?? $row['apellidos'] ?? '');

        if (empty($nombres) && empty($apellidos)) {
            return true;
        }

        $primerValor = is_array($row) ? reset($row) : '';
        if (is_string($primerValor) && (
                strpos(strtoupper($primerValor), 'NOTA') !== false ||
                strpos(strtoupper($primerValor), 'IMPORTANTE') !== false
            )) {
            return true;
        }

        return false;
    }

    public function model(array $row)
    {
        $this->currentRow++;

        if ($this->isRowEmpty($row)) {
            return null;
        }

        // Obtener valores
        $id = $this->getValueFromRow($row, ['ID', 'id']);
        $nombres = $this->getValueFromRow($row, ['NOMBRES', 'Nombres', 'FIRST_NAME']);
        $apellidos = $this->getValueFromRow($row, ['APELLIDOS', 'Apellidos', 'LAST_NAME']);
        $rol = $this->getValueFromRow($row, ['ROL', 'Rol', 'ROLE']);
        $tipoDocumento = $this->getValueFromRow($row, ['TIPO_DOCUMENTO', 'Tipo Documento', 'DOCUMENT_TYPE']);
        $numeroDocumento = $this->getValueFromRow($row, ['NUMERO_DOCUMENTO', 'Numero Documento', 'DOCUMENT_NUMBER']);
        $uciId = $this->getValueFromRow($row, ['UCI_ID', 'Uci Id', 'UCI']);
        $genero = $this->getValueFromRow($row, ['GENERO', 'Genero', 'GENDER']);
        $fechaNacimiento = $this->getValueFromRow($row, ['FECHA_DE_NACIMIENTO', 'Fecha Nacimiento', 'BIRTH_DATE']);
        $categoriaExcel = $this->getValueFromRow($row, ['CATEGORIA', 'Categoria', 'CATEGORY']);

        if (empty($rol)) {
            $rol = 'Atleta';
        }

        if (empty($nombres) && empty($apellidos)) {
            return null;
        }

        if (empty($nombres)) {
            $this->errors[] = "Fila {$this->currentRow}: El campo NOMBRES es obligatorio";
            return null;
        }
        if (empty($apellidos)) {
            $this->errors[] = "Fila {$this->currentRow}: El campo APELLIDOS es obligatorio";
            return null;
        }

        // Procesar STAFF
        if (strtoupper($rol) === 'STAFF' || strtoupper($categoriaExcel) === 'STAFF') {
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
        if (empty($genero)) {
            $this->errors[] = "Fila {$this->currentRow}: El campo GENERO es obligatorio. Usa 'Masculino' o 'Femenino'";
            return null;
        }

        $generoNormalizado = ucfirst(strtolower(trim($genero)));
        if (!in_array($generoNormalizado, ['Masculino', 'Femenino'])) {
            $this->errors[] = "Fila {$this->currentRow}: Género '{$genero}' no válido";
            return null;
        }

        // Validar fecha de nacimiento
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
            $this->errors[] = "Fila {$this->currentRow}: Edad mínima 3 años";
            return null;
        }
        if ($age > 18) {
            $this->errors[] = "Fila {$this->currentRow}: Edad máxima 18 años";
            return null;
        }

        $categoriaCalculada = $this->calculateCategoryByAge($birthDate, $generoNormalizado);
        if (!$categoriaCalculada) {
            $this->errors[] = "Fila {$this->currentRow}: No se pudo determinar categoría";
            return null;
        }

        $this->athleteCount++;
        $this->importedCount++;

        $athlete = new Athlete([
            'first_name' => strtoupper($nombres),
            'last_name' => strtoupper($apellidos),
            'dorsal_number' => '',  // NO guardar dorsal ahora
            'team_id' => $this->teamId,
            'document_type' => $tipoDocumento ?: null,
            'document_number' => $numeroDocumento ?: null,
            'uci_id' => $uciId ?: null,
            'gender' => $generoNormalizado,
            'category' => $categoriaCalculada,
            'birth_date' => $birthDate,
            'nationality' => 'Venezolana',
            'is_active' => true
        ]);

        $this->athletesList[] = $athlete;

        return $athlete;
    }

    public function rules(): array
    {
        return [];
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

    public function getAthletes()
    {
        return $this->athletesList;
    }
}
