<?php
// app/Http/Controllers/PublicRegistrationController.php

namespace App\Http\Controllers;

use App\Exports\TeamTemplateExport;
use App\Models\Team;
use App\Models\Athlete;
use App\Models\TeamStaff;
use App\Models\TeamMigrationData;
use App\Models\TeamMigrationVehicle;
use App\Models\Registration;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeamAthletesImport;

class PublicRegistrationController extends Controller
{
    public function downloadTemplateExcel()
    {
        return Excel::download(new TeamTemplateExport(), 'plantilla_inscripcion_equipos.xlsx');
    }

    private function getActiveEvent()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            $event = Event::create([
                'year'               => 2026,
                'name'               => 'Vuelta a la Fría 2026',
                'description'        => 'Edición 2026 de la Vuelta Menor a La Fría',
                'start_date'         => '2026-06-11',
                'end_date'           => '2026-06-14',
                'registration_start' => '2025-05-25',
                'registration_end'   => '2026-06-08',
                'is_active'          => true
            ]);
        }
        return $event;
    }

    public function showForm()
    {
        $categories = $this->getCategories();
        $transportTypes = ['Aéreo', 'Terrestre', 'Marítimo', 'Mixto'];
        $documentTypes = ['V', 'E', 'P', 'Pasaporte', 'Cedula'];

        return view('home.registration.team', compact('categories', 'transportTypes', 'documentTypes'));
    }

    public function downloadTemplate()
    {
        return Excel::download(new TeamTemplateExport(), 'plantilla_inscripcion_equipos.xlsx');
    }

    public function submitRegistration(Request $request)
    {
        \Log::info('Datos recibidos:', $request->all());

        // Validación inicial
        $validator = Validator::make($request->all(), [
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
            'vehicles' => 'nullable|array',
            'accept_terms' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_error', 'team');
        }

        // INICIAR TRANSACCIÓN - Todo o nada
        DB::beginTransaction();

        $team = null;
        $import = null;

        try {
            $event = $this->getActiveEvent();

            // 1. Verificar si ya existe un equipo con ese nombre
            $existingTeam = Team::where('name', $request->team_name)->first();
            if ($existingTeam) {
                throw new \Exception('Ya existe un equipo con el nombre "' . $request->team_name . '". Por favor contacta a la organización.');
            }

            // 2. Crear el equipo
            $team = Team::create([
                'name' => $request->team_name,
                'city' => $request->team_city,
                'country' => $request->team_country,
                'contact_email' => $request->delegate_email,
                'contact_phone' => $request->delegate_phone,
                'access_code' => Str::upper(Str::random(8)),
                'is_active' => true
            ]);

            if (!$team) {
                throw new \Exception('No se pudo crear el equipo. Intente nuevamente.');
            }

            // 3. Guardar datos migratorios
            if ($request->arrival_date || $request->return_date) {
                $migrationData = TeamMigrationData::create([
                    'team_id' => $team->id,
                    'arrival_date' => $request->arrival_date,
                    'arrival_border' => $request->arrival_border,
                    'transport_type' => $request->transport_type,
                    'return_date' => $request->return_date,
                    'return_flight_time' => $request->return_flight_time,
                    'notes' => $request->migration_notes
                ]);
                // Si falla, lanzará excepción automáticamente
            }

            // 4. Guardar vehículos
            if ($request->has('vehicles') && is_array($request->vehicles)) {
                foreach ($request->vehicles as $vehicle) {
                    if (!empty($vehicle['brand']) || !empty($vehicle['plate'])) {
                        TeamMigrationVehicle::create([
                            'team_id' => $team->id,
                            'brand' => $vehicle['brand'] ?? null,
                            'model' => $vehicle['model'] ?? null,
                            'plate' => $vehicle['plate'] ?? null,
                            'year' => $vehicle['year'] ?? null,
                            'color' => $vehicle['color'] ?? null,
                            'additional_info' => $vehicle['additional_info'] ?? null
                        ]);
                    }
                }
            }

            // 5. Procesar el archivo Excel
            $import = new TeamAthletesImport($team->id);
            Excel::import($import, $request->file('excel_file'));

            $errors = $import->getErrors();
            $athleteCount = $import->getAthleteCount();
            $staffCount = $import->getStaffCount();
            $totalImported = $import->getImportedCount();

            // Si hay errores en el Excel, hacer ROLLBACK
            if (count($errors) > 0) {
                $errorMessage = "Errores en el archivo Excel:\n" . implode("\n", $errors);
                throw new \Exception($errorMessage);
            }

            // Verificar que haya al menos un atleta
            if ($athleteCount === 0) {
                throw new \Exception('El archivo Excel no contiene ningún atleta válido. Debe haber al menos un atleta en el equipo.');
            }

            // 6. Registrar la inscripción del equipo
            $registration = Registration::create([
                'event_id' => $event->id,
                'registration_type' => 'team',
                'team_id' => $team->id,
                'email' => $request->delegate_email,
                'phone' => $request->delegate_phone,
                'status' => 'pending',
                'notes' => json_encode([
                    'delegate_name' => $request->delegate_name,
                    'delegate_whatsapp' => $request->delegate_whatsapp,
                    'region' => $request->region,
                    'athletes_count' => $athleteCount,
                    'staff_count' => $staffCount,
                    'total_imported' => $totalImported
                ]),
                'registered_at' => now()
            ]);

            // SI LLEGAMOS HASTA AQUÍ, TODO ESTÁ BIEN
            // Confirmar la transacción
            DB::commit();

            // Redirigir con éxito
            return redirect()->route('home')
                ->with('team_success', '¡Inscripción exitosa!')
                ->with('team_name', $team->name)
                ->with('access_code', $team->access_code)
                ->with('athletes_count', $athleteCount)
                ->with('staff_count', $staffCount)
                ->with('form_error', 'none');

        } catch (\Exception $e) {
            // HACER ROLLBACK DE TODO - Nada se guarda en la base de datos
            DB::rollBack();

            // Registrar el error en el log del servidor
            \Log::error('ERROR EN INSCRIPCIÓN DE EQUIPO', [
                'error' => $e->getMessage(),
                'team_name' => $request->team_name ?? null,
                'delegate_email' => $request->delegate_email ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            // Mensaje amigable para el usuario
            $userMessage = 'Ocurrió un error al procesar la inscripción. No se ha guardado ningún dato.';

            if ($e->getMessage()) {
                $userMessage .= ' Motivo: ' . $e->getMessage();
            }

            // Retornar error al usuario con el formulario visible nuevamente
            return redirect()->back()
                ->with('error', $userMessage)
                ->with('import_errors', $import ? $import->getErrors() : [])
                ->withInput()
                ->with('form_error', 'team');
        }
    }

    public function success()
    {
        if (!session('team_success')) {
            return redirect()->route('home');
        }

        return view('home.registration.success');
    }

    private function getCategories()
    {
        return [
            'COMPOTAS' => '3-4 años',
            'INICIACIÓN A' => '5-6 años',
            'INICIACIÓN B' => '7-8 años',
            'INICIACIÓN C' => '9-10 años',
            'PRE-INFANTIL' => '11-12 años',
            'INFANTIL' => '13-14 años',
            'PRE-JUVENIL' => '15-16 años',
            'JUVENIL' => '17-18 años'
        ];
    }
}
