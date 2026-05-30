<?php
// app/Http/Controllers/RegistrationController.php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Athlete;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    // Formulario inscripción individual
    public function individualForm()
    {
        $categories = $this->getCategories();
        return view('home.registration.individual', compact('categories'));
    }

    // Procesar inscripción individual
    public function individualSubmit(Request $request)
    {
        // Validación con mensajes personalizados en español
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Masculino,Femenino',
            'category' => 'required|string',
            'birth_date' => 'required|date|before:today',
            'emergency_contact' => 'required|string',
            'accept_terms' => 'required|accepted'
        ], [
            // Mensajes personalizados en español
            'first_name.required' => 'El campo Nombres es obligatorio.',
            'last_name.required' => 'El campo Apellidos es obligatorio.',
            'email.required' => 'El campo Email es obligatorio.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',
            'phone.required' => 'El campo Teléfono/WhatsApp es obligatorio.',
            'gender.required' => 'Debes seleccionar tu género.',
            'gender.in' => 'El género seleccionado no es válido.',
            'category.required' => 'Debes seleccionar una categoría.',
            'birth_date.required' => 'La fecha de nacimiento es obligatoria.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'emergency_contact.required' => 'El contacto de emergencia es obligatorio.',
            'accept_terms.required' => 'Debes aceptar los términos y condiciones.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('form_error', 'individual'); // Indicar qué formulario mostró error
        }

        // Generar número de dorsal automático
        $dorsalNumber = $this->generateDorsalNumber();

        // Crear el atleta
        $athlete = Athlete::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'dorsal_number' => $dorsalNumber,
            'team_id' => null, // Individual no tiene equipo
            'gender' => $request->gender,
            'category' => $request->category,
            'birth_date' => $request->birth_date,
            'nationality' => 'Venezolana',
            'is_active' => true
        ]);

        // Registrar la inscripción
        $registration = Registration::create([
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
            ->with('individual_success', '¡Inscripción registrada exitosamente! Tu dorsal es: ' . $dorsalNumber)
            ->with('form_success', 'individual');
    }

    // Verificar estado de inscripción
    public function checkStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $registration = Registration::where('email', $request->email)
            ->with(['team', 'athlete'])
            ->latest()
            ->first();

        if (!$registration) {
            return redirect()->back()->with('error', 'No se encontró ninguna inscripción con ese correo');
        }

        return view('home.registration.status', compact('registration'));
    }

    private function generateDorsalNumber()
    {
        $lastAthlete = Athlete::orderBy('id', 'desc')->first();
        $number = $lastAthlete ? intval(substr($lastAthlete->dorsal_number, 1)) + 1 : 1;
        return 'D' . str_pad($number, 4, '0', STR_PAD_LEFT);
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
