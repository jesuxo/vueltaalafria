<?php
// app/Helpers/WhatsAppHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    protected $token;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.from_phone_number_id');
    }

    /**
     * Enviar notificación de inscripción individual usando API directa de Meta
     */
    public function sendIndividualRegistration($phone, $name, $dorsal, $category)
    {
        try {
            // Limpiar el número de teléfono (solo números)
            $phone = $this->cleanPhoneNumber($phone);

            // Construir el mensaje usando plantilla
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phone,
                'type' => 'template',
                'template' => [
                    'name' => 'individual_registration_confirmation',
                    'language' => [
                        'code' => 'es'
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $name],
                                ['type' => 'text', 'text' => $dorsal],
                                ['type' => 'text', 'text' => $category],
                                ['type' => 'text', 'text' => '12-14 Junio 2026']
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages", $data);

            if ($response->successful()) {
                Log::info("WhatsApp individual enviado a {$phone}");
                return true;
            } else {
                Log::error("Error WhatsApp: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp individual: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación de inscripción de equipo
     */
    public function sendTeamRegistration($phone, $teamName, $accessCode, $athletesCount, $staffCount)
    {
        try {
            $phone = $this->cleanPhoneNumber($phone);

            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phone,
                'type' => 'template',
                'template' => [
                    'name' => 'team_registration_confirmation',
                    'language' => [
                        'code' => 'es'
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $teamName],
                                ['type' => 'text', 'text' => $accessCode],
                                ['type' => 'text', 'text' => (string)$athletesCount],
                                ['type' => 'text', 'text' => (string)$staffCount],
                                ['type' => 'text', 'text' => '12-14 Junio 2026']
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages", $data);

            if ($response->successful()) {
                Log::info("WhatsApp equipo enviado a {$phone}");
                return true;
            } else {
                Log::error("Error WhatsApp equipo: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp equipo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje de texto simple (solo dentro de ventana de 24h después de plantilla)
     */
    public function sendTextMessage($phone, $message)
    {
        try {
            $phone = $this->cleanPhoneNumber($phone);

            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $message
                ]
            ];

            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages", $data);

            if ($response->successful()) {
                Log::info("WhatsApp texto enviado a {$phone}");
                return true;
            } else {
                Log::error("Error WhatsApp texto: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp texto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar estado de la conexión WhatsApp
     */
    public function checkConnection()
    {
        try {
            $response = Http::withToken($this->token)
                ->get("https://graph.facebook.com/v19.0/{$this->phoneNumberId}");

            if ($response->successful()) {
                Log::info("WhatsApp conexión OK");
                return true;
            } else {
                Log::error("WhatsApp conexión fallida: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error verificando conexión WhatsApp: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Limpiar número de teléfono (eliminar +, espacios, guiones)
     */
    private function cleanPhoneNumber($phone)
    {
        // Eliminar cualquier carácter que no sea número
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Si el número comienza con 0, eliminarlo
        if (substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        }

        // Asegurar que tiene código de país (si no tiene, agregar 58 para Venezuela)
        if (strlen($phone) === 10) {
            $phone = '58' . $phone;
        }

        return $phone;
    }
}
