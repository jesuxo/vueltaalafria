<?php
// app/Http/Controllers/PublicRegistrationController.php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Athlete;
use App\Models\TeamMigrationData;
use App\Models\TeamMigrationVehicle;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeamAthletesImport;

class PublicRegistrationController extends Controller
{
    // Mostrar formulario de inscripción
    public function showForm()
    {
        $categories = $this->getCategories();
        $transportTypes = ['Aéreo', 'Terrestre', 'Marítimo', 'Mixto'];
        $documentTypes = ['V', 'E', 'P', 'Pasaporte', 'Cedula'];

        return view('home.registration.team', compact('categories', 'transportTypes', 'documentTypes'));
    }

    // Descargar plantilla Excel
    public function downloadTemplate()
    {
        $headers = [
            'ID',
            'APELLIDOS',
            'NOMBRES',
            'FECHA_DE_NACIMIENTO (dd/mm/aaaa)',
            'TIPO_DOCUMENTO (V/E/P/Pasaporte/Cedula)',
            'NUMERO_DOCUMENTO',
            'UCI_ID',
            'CATEGORIA',
            'GENERO (Masculino/Femenino)'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            // Agregar BOM para UTF-8 en Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);

            // Agregar fila de ejemplo
            fputcsv($file, [
                '1',
                'GARCIA',
                'JUAN',
                '15/05/2010',
                'V',
                '12345678',
                'UCI123456',
                'JUVENIL',
                'Masculino'
            ]);

            fputcsv($file, [
                '2',
                'RODRIGUEZ',
                'MARIA',
                '20/08/2011',
                'Pasaporte',
                'ABC123456',
                'UCI789012',
                'JUVENIL',
                'Femenino'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="plantilla_inscripcion_equipos.csv"',
        ]);
    }

    // Procesar inscripción
    public function submitRegistration(Request $request)
    {
        // Validación con Google reCAPTCHA (para evitar robots)
        $request->validate([
            'team_name' => 'required|string|max:255',
            'team_country' => 'required|string|max:255',
            'team_city' => 'nullable|string|max:255',
            'delegate_name' => 'required|string|max:255',
            'delegate_phone' => 'required|string|max:20',
            'delegate_email' => 'required|email|max:255',
            'delegate_whatsapp' => 'nullable|string|max:20',
            'region' => 'nullable|string|max:255',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'arrival_date' => 'nullable|date',
            'arrival_border' => 'nullable|string|max:255',
            'transport_type' => 'nullable|string|max:100',
            'return_date' => 'nullable|date',
            'return_flight_time' => 'nullable',
            'g-recaptcha-response' => 'required' // reCAPTCHA
        ]);

        // Verificar reCAPTCHA
        $this->verifyRecaptcha($request->input('g-recaptcha-response'));

        // Verificar si ya existe un equipo con ese nombre
        $existingTeam = Team::where('name', $request->team_name)->first();

        if ($existingTeam) {
            return back()->with('error', 'Ya existe un equipo con ese nombre. Por favor contacta a la organización.');
        }

        // Crear el equipo
        $team = Team::create([
            'name' => $request->team_name,
            'city' => $request->team_city,
            'country' => $request->team_country,
            'contact_email' => $request->delegate_email,
            'contact_phone' => $request->delegate_phone,
            'access_code' => Str::upper(Str::random(8)),
            'is_active' => true
        ]);

        // Guardar datos migratorios
        if ($request->arrival_date || $request->return_date) {
            TeamMigrationData::create([
                'team_id' => $team->id,
                'arrival_date' => $request->arrival_date,
                'arrival_border' => $request->arrival_border,
                'transport_type' => $request->transport_type,
                'return_date' => $request->return_date,
                'return_flight_time' => $request->return_flight_time,
                'notes' => $request->migration_notes
            ]);
        }

        // Procesar el archivo Excel
        try {
            $import = new TeamAthletesImport($team->id);
            Excel::import($import, $request->file('excel_file'));

            $errors = $import->getErrors();
            $importedCount = $import->getImportedCount();

            if (count($errors) > 0) {
                // Si hay errores, eliminamos el equipo y mostramos los errores
                $team->delete();
                return back()->with('import_errors', $errors)->withInput();
            }

            // Registrar la inscripción
            Registration::create([
                'registration_type' => 'team',
                'team_id' => $team->id,
                'email' => $request->delegate_email,
                'phone' => $request->delegate_phone,
                'status' => 'pending',
                'notes' => json_encode([
                    'delegate_name' => $request->delegate_name,
                    'delegate_whatsapp' => $request->delegate_whatsapp,
                    'region' => $request->region,
                    'athletes_count' => $importedCount
                ]),
                'registered_at' => now()
            ]);

            // Enviar email con código de acceso (opcional)
            // Mail::to($request->delegate_email)->send(new TeamRegistrationConfirmation($team, $importedCount));

            return redirect()->route('registration.team.success')
                ->with('success', '¡Inscripción exitosa!')
                ->with('team_name', $team->name)
                ->with('access_code', $team->access_code)
                ->with('athletes_count', $importedCount);

        } catch (\Exception $e) {
            $team->delete();
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage())->withInput();
        }
    }

    // Página de éxito
    public function success()
    {
        if (!session('success')) {
            return redirect()->route('registration.team.form');
        }

        return view('home.registration.success');
    }

    // Verificar reCAPTCHA
    private function verifyRecaptcha($token)
    {
        $secret = env('RECAPTCHA_SECRET_KEY');
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$token}");
        $data = json_decode($response);

        if (!$data->success) {
            throw new \Exception('Verificación anti-robots fallida. Por favor intenta nuevamente.');
        }
    }

    private function getCategories()
    {
        return [
            'COMPOTAS' => '3-4 años (2022-2023)',
            'INICIACIÓN A' => '5-6 años (2020-2021)',
            'INICIACIÓN B' => '7-8 años (2018-2019)',
            'EXHIBICIÓN' => '9-10 años (2016-2017)',
            'PRE-INFANTIL D' => '11-12 años (2014-2015)',
            'INFANTIL' => '13-14 años (2012-2013)',
            'PRE-JUVENIL' => '15-16 años (2010-2011)',
            'JUVENIL' => '17-18 años (2008-2009)'
        ];
    }
}
