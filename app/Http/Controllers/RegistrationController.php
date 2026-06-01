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
    // Obtener evento activo
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

    // Validar edad por categoría
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

    // Generar dorsal para el evento
    private function generateDorsalNumberForEvent($eventId)
    {
        $lastParticipation = AthleteEventParticipation::where('event_id', $eventId)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastParticipation ? intval(substr($lastParticipation->dorsal_number, 1)) + 1 : 1;
        return 'D' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Buscar o crear equipo/estructura
    private function findOrCreateTeam($structureName)
    {
        if (empty($structureName)) {
            return null;
        }

        // Buscar equipo existente por nombre exacto
        $team = Team::where('name', $structureName)->first();

        if ($team) {
            return $team;
        }

        // Verificar nuevamente por si hay diferencia en mayúsculas/minúsculas
        $team = Team::whereRaw('LOWER(name) = ?', [strtolower($structureName)])->first();

        if ($team) {
            return $team;
        }

        // Crear nuevo equipo/estructura
        $team = Team::create([
            'name'        => $structureName,
            'country'     => 'Venezuela',
            'is_active'   => true,
            'access_code' => Str::upper(Str::random(8))
        ]);

        return $team;
    }

    // Mapear categoría
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

    // Obtener categorías
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

    // Formulario inscripción individual
    public function individualForm()
    {
        $event = $this->getActiveEvent();
        $categories = $this->getCategories();
        return view('home.registration.individual', compact('categories', 'event'));
    }

    // Procesar inscripción individual
    public function individualSubmit(Request $request)
    {
        // Validación
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
            'structure_name' => 'required_if:has_structure,1|nullable|string|max:255'
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

        // Validar fechas de inscripción
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

        // Validar edad vs categoría
        $age = Carbon::parse($request->birth_date)->age;
        if (!$this->validateAgeByCategory($request->birth_date, $request->category, $request->gender)) {
            return redirect()->route('home')
                ->withErrors(['category' => "La categoría seleccionada no corresponde con tu edad ({$age} años)."])
                ->withInput()
                ->with('form_error', 'individual');
        }

        // Procesar estructura (equipo)
        // Procesar estructura (equipo)
        $teamId = null;
        $structureName = null;

        if ($request->has_structure == '1') {
            if ($request->structure_id) {
                // Usar estructura existente
                $team = Team::find($request->structure_id);
                if ($team) {
                    $teamId = $team->id;
                    $structureName = $team->name;
                }
            } elseif ($request->structure_name) {
                // Crear nueva estructura
                $structureName = $request->structure_name;
                $team = $this->findOrCreateTeam($structureName);
                if ($team) {
                    $teamId = $team->id;
                }
            }
        }

        // Verificar si el ciclista ya existe
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
            // Verificar si ya está inscrito en este evento específico
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

            // Si el atleta no tenía equipo y ahora tiene, actualizar
            if ($teamId && !$athlete->team_id) {
                $athlete->team_id = $teamId;
                $athlete->save();
            }
        } else {
            // Crear nuevo atleta
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

        // Generar dorsal para este evento
       // $dorsalNumber = $this->generateDorsalNumberForEvent($event->id);

        // Actualizar dorsal del atleta
        //$athlete->dorsal_number = $dorsalNumber;
        $athlete->save();

        // Crear participación en el evento
        AthleteEventParticipation::create([
            'athlete_id'    => $athlete->id,
            'event_id'      => $event->id,
            'dorsal_number' => '',
            'team_id'       => $teamId,
            'status'        => 'registered'
        ]);

        // Registrar la inscripción
        $registration = Registration::create([
            'event_id'          => $event->id,
            'registration_type' => 'individual',
            'athlete_id'        => $athlete->id,
            'team_id'           => $teamId,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'status'            => 'pending',
            'notes'             => json_encode([
                'emergency_contact' => $request->emergency_contact,
                'structure_name'    => $structureName,
                'has_structure'     => $request->has_structure
            ]),
            'registered_at'     => now()
        ]);

        // Preparar mensaje de éxito con información de la estructura
        $structureMessage = '';
        if ($structureName) {
            $structureMessage = " Representas a: {$structureName}.";
        }

        // Redirigir con modal de éxito
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
                'estructura' => $structureName ?: 'Independiente'
            ])
            ->with('form_error', 'none');
    }

    // Verificar estado de inscripción
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

    // Obtener rango de edad de la categoría
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
