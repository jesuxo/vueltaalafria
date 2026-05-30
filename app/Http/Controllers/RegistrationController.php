<?php
// app/Http/Controllers/RegistrationController.php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Athlete;
use App\Models\AthleteEventParticipation;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    // Obtener evento activo
    private function getActiveEvent()
    {
        $event = Event::where('is_active', true)->first();
        if (!$event) {
            throw new \Exception('No hay un evento activo para inscripciones');
        }
        return $event;
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
        $event = $this->getActiveEvent();

        // Validar fechas de inscripción
        if (!$event->isRegistrationOpen()) {
            return redirect()->back()
                ->withErrors(['error' => 'El período de inscripción para este evento ha cerrado.'])
                ->withInput();
        }

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
            'identification_document' => 'nullable|string|max:50'
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
            'accept_terms.accepted' => 'Debes aceptar los términos y condiciones.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_error', 'individual');
        }

        // Verificar si el ciclista ya existe por documento o email
        $existingAthlete = Athlete::where('document_number', $request->identification_document)
            ->orWhere(function($q) use ($request) {
                $q->where('first_name', $request->first_name)
                    ->where('last_name', $request->last_name)
                    ->where('birth_date', $request->birth_date);
            })->first();

        if ($existingAthlete) {
            // Verificar si ya está inscrito en este evento
            $existingRegistration = Registration::where('event_id', $event->id)
                ->where('athlete_id', $existingAthlete->id)
                ->exists();

            if ($existingRegistration) {
                return redirect()->back()
                    ->withErrors(['error' => 'Este ciclista ya está inscrito para la edición ' . $event->year . '.'])
                    ->withInput();
            }

            $athlete = $existingAthlete;
        } else {
            // Crear nuevo atleta
            $athlete = Athlete::create([
                'first_name' => strtoupper($request->first_name),
                'last_name' => strtoupper($request->last_name),
                'dorsal_number' => null, // Se asignará después
                'team_id' => null,
                'gender' => $request->gender,
                'category' => $this->mapCategory($request->category, $request->gender),
                'birth_date' => $request->birth_date,
                'nationality' => $request->nationality ?? 'Venezolana',
                'document_type' => $request->document_type ?? 'V',
                'document_number' => $request->identification_document,
                'is_active' => true
            ]);
        }

        // Generar dorsal para este evento
        $dorsalNumber = $this->generateDorsalNumberForEvent($event->id);

        // Actualizar dorsal del atleta para este evento
        $athlete->dorsal_number = $dorsalNumber;
        $athlete->save();

        // Crear participación en el evento
        AthleteEventParticipation::create([
            'athlete_id' => $athlete->id,
            'event_id' => $event->id,
            'dorsal_number' => $dorsalNumber,
            'status' => 'registered'
        ]);

        // Registrar la inscripción
        $registration = Registration::create([
            'event_id' => $event->id,
            'registration_type' => 'individual',
            'athlete_id' => $athlete->id,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'pending',
            'notes' => json_encode([
                'emergency_contact' => $request->emergency_contact
            ]),
            'registered_at' => now()
        ]);

        return redirect()->route('home')
            ->with('individual_success', '¡Inscripción registrada exitosamente para la ' . $event->name . '! Tu dorsal es: ' . $dorsalNumber)
            ->with('form_success', 'individual');
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
            return redirect()->back()->with('check_error', 'No se encontró ninguna inscripción para este evento con ese correo.');
        }

        return view('home.registration.status', compact('registration', 'event'));
    }

    private function generateDorsalNumberForEvent($eventId)
    {
        $lastParticipation = AthleteEventParticipation::where('event_id', $eventId)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastParticipation ? intval(substr($lastParticipation->dorsal_number, 1)) + 1 : 1;
        return 'D' . str_pad($number, 4, '0', STR_PAD_LEFT);
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
            'Exhibición' => 'Exhibición (9-10 años)',
            'Compota Strider' => 'Compota Strider (3-4 años)',
            'Compota Pedales' => 'Compota Pedales (3-4 años)'
        ];
    }
}
