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

    public function onError(Throwable $e)
    {
        $this->errors[] = 'Error en fila ' . $this->currentRow . ': ' . $e->getMessage();
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
        // Buscar las columnas clave sin importar cómo se llamen
        $nombres = '';
        $apellidos = '';

        foreach ($row as $key => $value) {
            $keyLower = strtolower(trim($key));
            $value = trim($value ?? '');

            if (strpos($keyLower, 'nombre') !== false || $keyLower === 'nombres') {
                $nombres = $value;
            }
            if (strpos($keyLower, 'apellido') !== false || $keyLower === 'apellidos') {
                $apellidos = $value;
            }
        }

        // Si no hay nombres ni apellidos, la fila está vacía
        if (empty($nombres) && empty($apellidos)) {
            return true;
        }

        // Verificar si es una fila de notas
        $primerValor = is_array($row) ? reset($row) : '';
        if (is_string($primerValor) && (
                strpos(strtoupper($primerValor), 'NOTA') !== false ||
                strpos(strtoupper($primerValor), 'IMPORTANTE') !== false
            )) {
            return true;
        }

        return false;
    }

    /**
     * Busca un valor en el row por múltiples posibles nombres de columna
     */
    private function getValueFromRow($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            // Buscar coincidencia exacta
            if (isset($row[$key]) && !empty(trim($row[$key]))) {
                return trim($row[$key]);
            }
            // Buscar coincidencia insensible a mayúsculas
            foreach ($row as $rowKey => $rowValue) {
                if (strtolower(trim($rowKey)) === strtolower($key)) {
                    return trim($rowValue);
                }
            }
            // Buscar si la clave contiene la palabra clave
            foreach ($row as $rowKey => $rowValue) {
                if (strpos(strtolower($rowKey), strtolower($key)) !== false) {
                    return trim($rowValue);
                }
            }
        }
        return '';
    }

    public function model(array $row)
    {
        $this->currentRow++;

        // Verificar si la fila está vacía o es una nota
        if ($this->isRowEmpty($row)) {
            return null;
        }

        // Obtener valores usando múltiples posibles nombres de columna
        $id = $this->getValueFromRow($row, ['ID', 'id', 'Id']);
        $nombres = $this->getValueFromRow($row, ['NOMBRES', 'Nombres', 'FIRST_NAME', 'Nombre', 'nombre']);
        $apellidos = $this->getValueFromRow($row, ['APELLIDOS', 'Apellidos', 'LAST_NAME', 'Apellido', 'apellido']);
        $rol = $this->getValueFromRow($row, ['ROL', 'Rol', 'ROLE', 'Role']);
        $tipoDocumento = $this->getValueFromRow($row, ['TIPO_DOCUMENTO', 'Tipo Documento', 'DOCUMENT_TYPE']);
        $numeroDocumento = $this->getValueFromRow($row, ['NUMERO_DOCUMENTO', 'Numero Documento', 'DOCUMENT_NUMBER']);
        $uciId = $this->getValueFromRow($row, ['UCI_ID', 'Uci Id', 'UCI']);
        $genero = $this->getValueFromRow($row, ['GENERO', 'Genero', 'GENDER', 'Gender', 'SEXO', 'Sexo']);
        $fechaNacimiento = $this->getValueFromRow($row, ['FECHA_DE_NACIMIENTO', 'Fecha Nacimiento', 'BIRTH_DATE', 'Nacimiento']);
        $categoriaExcel = $this->getValueFromRow($row, ['CATEGORIA', 'Categoria', 'CATEGORY', 'Category']);

        // Si el rol está vacío, asumir "Atleta"
        if (empty($rol)) {
            $rol = 'Atleta';
        }

        // Validar datos mínimos
        if (empty($nombres) && empty($apellidos)) {
            return null;
        }

        if (empty($nombres)) {
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): El campo NOMBRES es obligatorio";
            return null;
        }
        if (empty($apellidos)) {
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): El campo APELLIDOS es obligatorio";
            return null;
        }

        // Procesar STAFF (si el rol es STAFF o la categoría es STAFF)
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
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): El campo GENERO es obligatorio. Usa 'Masculino' o 'Femenino'";
            return null;
        }

        $generoNormalizado = ucfirst(strtolower(trim($genero)));
        if (!in_array($generoNormalizado, ['Masculino', 'Femenino'])) {
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): Género '{$genero}' no válido. Use Masculino o Femenino";
            return null;
        }

        // Validar fecha de nacimiento
        if (empty($fechaNacimiento)) {
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): La fecha de nacimiento es obligatoria";
            return null;
        }

        $birthDate = $this->parseDate($fechaNacimiento);
        if (!$birthDate) {
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): Formato de fecha inválido. Use dd/mm/aaaa. Valor recibido: {$fechaNacimiento}";
            return null;
        }

        $age = Carbon::parse($birthDate)->age;

        if ($age < 3) {
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): Edad {$age} años. Edad mínima permitida: 3 años";
            return null;
        }
        if ($age > 18) {
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): Edad {$age} años. Edad máxima permitida: 18 años";
            return null;
        }

        // Calcular categoría automáticamente
        $categoriaCalculada = $this->calculateCategoryByAge($birthDate, $generoNormalizado);
        if (!$categoriaCalculada) {
            $this->errors[] = "Fila {$this->currentRow} (ID: {$id}): No se pudo determinar categoría para edad {$age} años";
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
            'nationality' => 'Venezolana',
            'is_active' => true
        ]);
    }

    public function rules(): array
    {
        return [];
    }

    public function customValidationMessages()
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
}
