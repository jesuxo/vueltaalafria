<?php
// app/Imports/AthletesImport.php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\Team;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Validator;

class AthletesImport implements ToModel, WithHeadingRow, WithValidation
{
    private $teamId;
    private $errors = [];

    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    public function model(array $row)
    {
        // Generar número de dorsal único
        $dorsalNumber = $this->generateDorsalNumber();

        return new Athlete([
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'dorsal_number' => $dorsalNumber,
            'team_id' => $this->teamId,
            'gender' => $row['gender'],
            'category' => $row['category'],
            'birth_date' => $row['birth_date'],
            'nationality' => $row['nationality'] ?? 'Venezolana',
            'is_active' => true
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Masculino,Femenino',
            'category' => 'required|in:Pre-Infantil Masculino,Pre-Infantil Femenino,Infantil Masculino,Infantil Femenino,Pre-Juvenil Masculino,Pre-Juvenil Femenino,Juvenil Masculino,Juvenil Femenino',
            'birth_date' => 'required|date|before:today',
            'nationality' => 'nullable|string'
        ];
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
}
