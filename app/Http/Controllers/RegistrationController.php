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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:Masculino,Femenino',
            'category' => 'required|string',
            'birth_date' => 'required|date|before:today',
            'nationality' => 'nullable|string|max:255',
            'emergency_contact' => 'required|string',
            'emergency_phone' => 'required|string',
            'accept_terms' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Registrar la inscripción
        $registration = Registration::create([
            'registration_type' => 'individual',
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'pending',
            'notes' => json_encode([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'category' => $request->category,
                'birth_date' => $request->birth_date,
                'nationality' => $request->nationality,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone
            ]),
            'registered_at' => now()
        ]);

        // Enviar email de confirmación (opcional)
        // Mail::to($request->email)->send(new RegistrationConfirmation($registration));

        return redirect()->route('home')
            ->with('success', '¡Inscripción registrada exitosamente! Pronto recibirás un correo de confirmación.');
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

    private function getCategories()
    {
        return [
            'Pre-Infantil Masculino',
            'Pre-Infantil Femenino',
            'Infantil Masculino',
            'Infantil Femenino',
            'Pre-Juvenil Masculino',
            'Pre-Juvenil Femenino',
            'Juvenil Masculino',
            'Juvenil Femenino'
        ];
    }
}
