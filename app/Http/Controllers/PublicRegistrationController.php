<?php
// app/Http/Controllers/PublicRegistrationController.php

namespace App\Http\Controllers;

use App\Exports\TeamTemplateExport;
use App\Helpers\WhatsAppHelper;
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
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamRegistrationMail;

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

    /**
     * Comprimir y guardar imagen directamente en public/img/comprobantes/
     */
    private function savePaymentProof($file, $teamId)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = time() . '_' . $teamId;

        // Directorio donde se guardarán los comprobantes
        $uploadDir = public_path('img/comprobantes');

        // Crear directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Procesar según el tipo de archivo
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            // Para imágenes: comprimir y convertir a JPG
            $finalFilename = $filename . '.jpg';
            $finalPath = $uploadDir . '/' . $finalFilename;

            // Obtener la imagen desde el archivo temporal
            $tempPath = $file->getPathname();
            $imageInfo = getimagesize($tempPath);

            if ($imageInfo) {
                // Crear imagen según el tipo original
                if ($extension == 'png') {
                    $image = imagecreatefrompng($tempPath);
                } else {
                    $image = imagecreatefromjpeg($tempPath);
                }

                if ($image) {
                    // Obtener dimensiones originales
                    $width = imagesx($image);
                    $height = imagesy($image);

                    // Redimensionar si es muy grande (máx 1200px)
                    $maxWidth = 1200;
                    if ($width > $maxWidth) {
                        $newWidth = $maxWidth;
                        $newHeight = intval($height * ($maxWidth / $width));
                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($image);
                        $image = $resized;
                    }

                    // Guardar con compresión (calidad 70%)
                    imagejpeg($image, $finalPath, 70);
                    imagedestroy($image);

                    return 'img/comprobantes/' . $finalFilename;
                }
            }

            // Si algo falló, guardar como está
            $file->move($uploadDir, $filename . '.' . $extension);
            return 'img/comprobantes/' . $filename . '.' . $extension;
        }
        elseif ($extension == 'pdf') {
            // Para PDF: solo mover
            $finalFilename = $filename . '.pdf';
            $file->move($uploadDir, $finalFilename);
            return 'img/comprobantes/' . $finalFilename;
        }
        else {
            // Otros formatos: mover como están
            $finalFilename = $filename . '.' . $extension;
            $file->move($uploadDir, $finalFilename);
            return 'img/comprobantes/' . $finalFilename;
        }
    }

    public function submitRegistration(Request $request)
    {
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
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string',
            'accept_terms' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_error', 'team');
        }

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
            $athletes = $import->getAthletes();

            // Si hay errores en el Excel, hacer ROLLBACK
            if (count($errors) > 0) {
                $errorMessage = "Errores en el archivo Excel:\n" . implode("\n", $errors);
                throw new \Exception($errorMessage);
            }

            // Verificar que haya al menos un atleta
            if ($athleteCount === 0) {
                throw new \Exception('El archivo Excel no contiene ningún atleta válido. Debe haber al menos un atleta en el equipo.');
            }

            // Calcular el monto total
            $totalAmount = $this->calculateTotalAmount($athletes);

            // ==============================================
            // GUARDAR COMPROBANTE DE PAGO
            // ==============================================
            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $this->savePaymentProof($request->file('payment_proof'), $team->id);
            }

            // 6. Registrar la inscripción del equipo
            $registration = Registration::create([
                'event_id' => $event->id,
                'registration_type' => 'team',
                'team_id' => $team->id,
                'email' => $request->delegate_email,
                'phone' => $request->delegate_phone,
                'status' => 'pending',
                'amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'payment_proof' => $paymentProofPath,
                'payment_status' => $request->payment_method === 'efectivo' ? 'pending' : 'pending',
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

            DB::commit();

            try {
                $whatsappHelper = new WhatsAppHelper();
                $phone = str_replace('+', '', $request->delegate_phone);
                $whatsappHelper->sendTeamRegistration(
                    $phone,
                    $team->name,
                    $team->access_code,
                    $athleteCount,
                    $staffCount
                );
            } catch (\Exception $e) {

            }

            // Redirigir con éxito y mostrar modal
            return redirect()->route('home')
                ->with('success_modal', true)
                ->with('success_title', '¡Inscripción de Equipo Registrada!')
                ->with('success_message', "¡El equipo {$team->name} se ha inscrito exitosamente para la {$event->name}!")
                ->with('success_details', [
                    'equipo' => $team->name,
                    'codigo' => $team->access_code,
                    'atletas' => $athleteCount,
                    'staff' => $staffCount,
                    'total' => '$' . number_format($totalAmount, 2),
                    'email' => $request->delegate_email
                ])
                ->with('form_error', 'none');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('ERROR EN INSCRIPCIÓN DE EQUIPO', [
                'error' => $e->getMessage(),
                'team_name' => $request->team_name ?? null,
                'delegate_email' => $request->delegate_email ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->with('import_errors', $import ? $import->getErrors() : [])
                ->withInput()
                ->with('form_error', 'team');
        }
    }

    private function calculateTotalAmount($athletes)
    {
        $categories3Days = ['Pre-Infantil Masculino', 'Pre-Infantil Femenino', 'Infantil Masculino', 'Infantil Femenino',
            'Pre-Juvenil Masculino', 'Pre-Juvenil Femenino', 'Juvenil Masculino', 'Juvenil Femenino'];
        $cost3Days = 30;
        $cost1Day = 15;

        $total = 0;
        foreach ($athletes as $athlete) {
            if (in_array($athlete->category, $categories3Days)) {
                $total += $cost3Days;
            } else {
                $total += $cost1Day;
            }
        }

        return $total;
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
