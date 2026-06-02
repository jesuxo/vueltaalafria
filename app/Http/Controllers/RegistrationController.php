<?php
// app/Http/Controllers/RegistrationController.php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Athlete;
use App\Models\Team;
use App\Models\AthleteEventParticipation;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegistrationController extends Controller
{
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

    /**
     * Guardar comprobante de pago para individual
     */
    private function savePaymentProofIndividual($file, $athleteId)
    {

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = time() . '_individual_' . $athleteId;

        // Directorio donde se guardarán los comprobantes
        $uploadDir = public_path('img/comprobantes');


        // Crear directorio si no existe
        if (!file_exists($uploadDir)) {

            $created = mkdir($uploadDir, 0777, true);

        }

        // Procesar según el tipo de archivo
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $finalFilename = $filename . '.jpg';
            $finalPath = $uploadDir . '/' . $finalFilename;
            $tempPath = $file->getPathname();


            // Verificar que GD esté instalado
            if (!extension_loaded('gd')) {
                // Guardar el archivo sin compresión
                $file->move($uploadDir, $finalFilename);
                return 'img/comprobantes/' . $finalFilename;
            }

            $imageInfo = getimagesize($tempPath);

            if ($imageInfo) {
                if ($extension == 'png') {
                    $image = imagecreatefrompng($tempPath);
                } else {
                    $image = imagecreatefromjpeg($tempPath);
                }

                if ($image) {
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $maxWidth = 1200;

                    if ($width > $maxWidth) {
                        $newWidth = $maxWidth;
                        $newHeight = intval($height * ($maxWidth / $width));
                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($image);
                        $image = $resized;
                    }

                    imagejpeg($image, $finalPath, 70);
                    imagedestroy($image);
                    return 'img/comprobantes/' . $finalFilename;
                } else {
                }
            } else {
            }

            $file->move($uploadDir, $filename . '.' . $extension);
            return 'img/comprobantes/' . $filename . '.' . $extension;
        }
        elseif ($extension == 'pdf') {
            $finalFilename = $filename . '.pdf';
            $file->move($uploadDir, $finalFilename);
            return 'img/comprobantes/' . $finalFilename;
        }
        else {
            $finalFilename = $filename . '.' . $extension;
            $file->move($uploadDir, $finalFilename);
            return 'img/comprobantes/' . $finalFilename;
        }
    }

    private function validateAgeByCategory($birthDate, $category, $gender)
    {
        $age = Carbon::parse($birthDate)->age;

        $ageRanges = [
            'Pre-Infantil' => ['min' => 11, 'max' => 12],
            'Infantil' => ['min' => 13, 'max' => 14],
            'Pre-Juvenil' => ['min' => 15, 'max' => 16],
            'Juvenil' => ['min' => 17, 'max' => 18],
            'Iniciación A' => ['min' => 5, 'max' => 6],
            'Iniciación B' => ['min' => 7, 'max' => 8],
            'Iniciación C' => ['min' => 9, 'max' => 10],
            'Exhibición' => ['min' => 9, 'max' => 10],
            'Compota Strider' => ['min' => 3, 'max' => 4],
            'Compota Pedales' => ['min' => 3, 'max' => 4]
        ];

        if (!isset($ageRanges[$category])) {
            return false;
        }

        $range = $ageRanges[$category];
        return $age >= $range['min'] && $age <= $range['max'];
    }

    private function generateDorsalNumberForEvent($eventId)
    {
        $lastParticipation = AthleteEventParticipation::where('event_id', $eventId)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastParticipation ? intval(substr($lastParticipation->dorsal_number, 1)) + 1 : 1;
        return 'D' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    private function findOrCreateTeam($structureName)
    {
        if (empty($structureName)) {
            return null;
        }

        $team = Team::where('name', $structureName)->first();

        if ($team) {
            return $team;
        }

        $team = Team::whereRaw('LOWER(name) = ?', [strtolower($structureName)])->first();

        if ($team) {
            return $team;
        }

        $team = Team::create([
            'name'        => $structureName,
            'country'     => 'Venezuela',
            'is_active'   => true,
            'access_code' => Str::upper(Str::random(8))
        ]);

        return $team;
    }

    private function mapCategory($category, $gender)
    {
        $mapping = [
            'Pre-Infantil' => ['Masculino' => 'Pre-Infantil Masculino', 'Femenino' => 'Pre-Infantil Femenino'],
            'Infantil' => ['Masculino' => 'Infantil Masculino', 'Femenino' => 'Infantil Femenino'],
            'Pre-Juvenil' => ['Masculino' => 'Pre-Juvenil Masculino', 'Femenino' => 'Pre-Juvenil Femenino'],
            'Juvenil' => ['Masculino' => 'Juvenil Masculino', 'Femenino' => 'Juvenil Femenino'],
            'Iniciación A' => 'Iniciación A',
            'Iniciación B' => 'Iniciación B',
            'Iniciación C' => 'Iniciación C',
            'Exhibición' => 'Exhibición',
            'Compota Strider' => 'Compota Strider',
            'Compota Pedales' => 'Compota Pedales'
        ];

        if (isset($mapping[$category]) && is_array($mapping[$category])) {
            return $mapping[$category][$gender] ?? null;
        }

        return $mapping[$category] ?? null;
    }

    private function getCategories()
    {
        return [
            'Pre-Infantil' => 'Pre-Infantil (11-12 años)',
            'Infantil' => 'Infantil (13-14 años)',
            'Pre-Juvenil' => 'Pre-Juvenil (15-16 años)',
            'Juvenil' => 'Juvenil (17-18 años)',
            'Iniciación A' => 'Iniciación A (5-6 años)',
            'Iniciación B' => 'Iniciación B (7-8 años)',
            'Iniciación C' => 'Iniciación C (9-10 años)',
            'Exhibición' => 'Exhibición (9-10 años)',
            'Compota Strider' => 'Compota Strider (3-4 años)',
            'Compota Pedales' => 'Compota Pedales (3-4 años)'
        ];
    }

    public function individualForm()
    {
        $event = $this->getActiveEvent();
        $categories = $this->getCategories();
        return view('home.registration.individual', compact('categories', 'event'));
    }

    public function individualSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Masculino,Femenino',
            'category' => 'required|string',
            'birth_date' => 'required|date|before:today',
            'emergency_contact' => 'required|string',
            'accept_terms' => 'required|accepted',
            'identification_document' => 'nullable|string|max:50',
            'has_structure' => 'nullable|in:0,1',
            'structure_name' => 'required_if:has_structure,1|nullable|string|max:255',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string',
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ], [
            'first_name.required' => 'El campo Nombres es obligatorio.',
            'last_name.required' => 'El campo Apellidos es obligatorio.',
            'email.required' => 'El campo Email es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'phone.required' => 'El campo Teléfono/WhatsApp es obligatorio.',
            'gender.required' => 'Debes seleccionar tu género.',
            'category.required' => 'Debes seleccionar una categoría.',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'emergency_contact.required' => 'El contacto de emergencia es obligatorio.',
            'accept_terms.accepted' => 'Debes aceptar los términos y condiciones.',
            'structure_name.required_if' => 'Debes ingresar el nombre de la Escuela/Club/Fundación/Sponsor que representas.'
        ]);

        if ($validator->fails()) {
            return redirect()->route('home')
                ->withErrors($validator)
                ->withInput()
                ->with('form_error', 'individual');
        }

        $event = $this->getActiveEvent();

        if (!$event->isRegistrationOpen()) {
            return redirect()->route('home')
                ->withErrors(['error' => 'El período de inscripción para este evento ha cerrado.'])
                ->withInput()
                ->with('form_error', 'individual');
        }

        if ($request->has_structure == '1' && !$request->structure_id && $request->structure_name) {
            $existingTeam = Team::where('name', $request->structure_name)->first();
            if ($existingTeam) {
                return redirect()->route('home')
                    ->withErrors(['structure_name' => 'Ya existe una estructura con el nombre "' . $request->structure_name . '". Por favor selecciónala de la lista.'])
                    ->withInput()
                    ->with('form_error', 'individual');
            }
        }

        $age = Carbon::parse($request->birth_date)->age;
        if (!$this->validateAgeByCategory($request->birth_date, $request->category, $request->gender)) {
            return redirect()->route('home')
                ->withErrors(['category' => "La categoría seleccionada no corresponde con tu edad ({$age} años)."])
                ->withInput()
                ->with('form_error', 'individual');
        }

        $teamId = null;
        $structureName = null;

        if ($request->has_structure == '1') {
            if ($request->structure_id) {
                $team = Team::find($request->structure_id);
                if ($team) {
                    $teamId = $team->id;
                    $structureName = $team->name;
                }
            } elseif ($request->structure_name) {
                $structureName = $request->structure_name;
                $team = $this->findOrCreateTeam($structureName);
                if ($team) {
                    $teamId = $team->id;
                }
            }
        }

        $existingAthlete = null;

        if ($request->identification_document) {
            $existingAthlete = Athlete::where('document_number', $request->identification_document)->first();
        }

        if (!$existingAthlete) {
            $existingAthlete = Athlete::where('first_name', 'LIKE', $request->first_name)
                ->where('last_name', 'LIKE', $request->last_name)
                ->where('birth_date', $request->birth_date)
                ->first();
        }

        if ($existingAthlete) {
            $existingParticipation = AthleteEventParticipation::where('event_id', $event->id)
                ->where('athlete_id', $existingAthlete->id)
                ->exists();

            if ($existingParticipation) {
                return redirect()->route('home')
                    ->withErrors(['error' => 'Este ciclista ya está inscrito para la ' . $event->name . '.'])
                    ->withInput()
                    ->with('form_error', 'individual');
            }

            $athlete = $existingAthlete;

            if ($teamId && !$athlete->team_id) {
                $athlete->team_id = $teamId;
                $athlete->save();
            }
        } else {
            $athlete = Athlete::create([
                'first_name'      => strtoupper($request->first_name),
                'last_name'       => strtoupper($request->last_name),
                'dorsal_number'   => null,
                'team_id'         => $teamId,
                'gender'          => $request->gender,
                'category'        => $this->mapCategory($request->category, $request->gender),
                'birth_date'      => $request->birth_date,
                'nationality'     => $request->nationality ?? 'Venezolana',
                'document_type'   => $request->document_type ?? 'V',
                'document_number' => $request->identification_document,
                'is_active'       => true
            ]);
        }

        $athlete->save();

        AthleteEventParticipation::create([
            'athlete_id'    => $athlete->id,
            'event_id'      => $event->id,
            'dorsal_number' => '',
            'team_id'       => $teamId,
            'status'        => 'registered'
        ]);

        // ==============================================
        // GUARDAR COMPROBANTE DE PAGO PARA INDIVIDUAL
        // ==============================================
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $this->savePaymentProofIndividual($request->file('payment_proof'), $athlete->id);
        }

        // Calcular el monto según la categoría
        $categories3Days = ['Pre-Infantil Masculino', 'Pre-Infantil Femenino', 'Infantil Masculino', 'Infantil Femenino',
            'Pre-Juvenil Masculino', 'Pre-Juvenil Femenino', 'Juvenil Masculino', 'Juvenil Femenino'];
        $amount = in_array($athlete->category, $categories3Days) ? 30 : 15;

        $registration = Registration::create([
            'event_id'          => $event->id,
            'registration_type' => 'individual',
            'athlete_id'        => $athlete->id,
            'team_id'           => $teamId,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'status'            => 'pending',
            'amount'            => $amount,
            'payment_method'    => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'payment_proof'     => $paymentProofPath,
            'payment_status'    => $request->payment_method === 'efectivo' ? 'pending' : 'pending',
            'notes'             => json_encode([
                'emergency_contact' => $request->emergency_contact,
                'structure_name'    => $structureName,
                'has_structure'     => $request->has_structure
            ]),
            'registered_at'     => now()
        ]);

        $structureMessage = '';
        if ($structureName) {
            $structureMessage = " Representas a: {$structureName}.";
        }

        return redirect()->route('home')
            ->with('success_modal', true)
            ->with('success_title', '¡Inscripción Registrada!')
            ->with('success_message', "¡Inscripción registrada exitosamente para la {$event->name}!{$structureMessage}")
            ->with('success_details', [
                'nombre'     => $athlete->first_name . ' ' . $athlete->last_name,
                'dorsal'     => '',
                'categoria'  => $athlete->category,
                'email'      => $request->email,
                'telefono'   => $request->phone,
                'estructura' => $structureName ?: 'Independiente',
                'total'      => '$' . number_format($amount, 2)
            ])
            ->with('form_error', 'none');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $event = $this->getActiveEvent();

        $registration = Registration::where('event_id', $event->id)
            ->where('email', $request->email)
            ->with(['team', 'athlete'])
            ->first();

        if (!$registration) {
            return redirect()->route('home')
                ->with('error', 'No se encontró ninguna inscripción para este evento con ese correo.')
                ->with('form_error', 'none');
        }

        return redirect()->route('home')
            ->with('show_status_modal', true)
            ->with('status_data', $registration)
            ->with('form_error', 'none');
    }

    private function getCategoryAgeRange($category)
    {
        $ageRanges = [
            'Pre-Infantil' => ' (11-12 años)',
            'Infantil' => ' (13-14 años)',
            'Pre-Juvenil' => ' (15-16 años)',
            'Juvenil' => ' (17-18 años)',
            'Iniciación A' => ' (5-6 años)',
            'Iniciación B' => ' (7-8 años)',
            'Iniciación C' => ' (9-10 años)',
            'Exhibición' => ' (9-10 años)',
            'Compota Strider' => ' (3-4 años)',
            'Compota Pedales' => ' (3-4 años)'
        ];

        return $ageRanges[$category] ?? '';
    }
}
