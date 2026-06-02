<?php
// app/Helpers/WhatsAppHelper.php

namespace App\Helpers;

use Netflie\WhatsAppCloudApi\WhatsApp;
use Netflie\WhatsAppCloudApi\Message\Template\Component;
use Netflie\WhatsAppCloudApi\Message\Template\Language;
use Netflie\WhatsAppCloudApi\Message\Template\Template;
use Illuminate\Support\Facades\Log;

class WhatsAppHelper
{
    protected $whatsapp;

    public function __construct()
    {
        $this->whatsapp = new WhatsApp([
            'from_phone_number_id' => config('services.whatsapp.from_phone_number_id'),
            'access_token' => config('services.whatsapp.token'),
        ]);
    }

    /**
     * Enviar notificación de inscripción individual
     */
    public function sendIndividualRegistration($phone, $name, $dorsal, $category)
    {
        try {
            $template = new Template('individual_registration_confirmation', new Language('es'));
            $template->addComponent(Component::create()->addParameter($name));
            $template->addComponent(Component::create()->addParameter($dorsal));
            $template->addComponent(Component::create()->addParameter($category));
            $template->addComponent(Component::create()->addParameter('12-14 Junio 2026'));

            $response = $this->whatsapp->sendTemplate($phone, $template);

            Log::info("WhatsApp individual enviado a {$phone}");
            return $response;
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
            $template = new Template('team_registration_confirmation', new Language('es'));
            $template->addComponent(Component::create()->addParameter($teamName));
            $template->addComponent(Component::create()->addParameter($accessCode));
            $template->addComponent(Component::create()->addParameter($athletesCount));
            $template->addComponent(Component::create()->addParameter($staffCount));
            $template->addComponent(Component::create()->addParameter('12-14 Junio 2026'));

            $response = $this->whatsapp->sendTemplate($phone, $template);

            Log::info("WhatsApp equipo enviado a {$phone}");
            return $response;
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp equipo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje de texto libre (solo dentro de ventana de 24h)
     */
    public function sendTextMessage($phone, $message)
    {
        try {
            $response = $this->whatsapp->sendTextMessage($phone, $message);
            Log::info("WhatsApp texto enviado a {$phone}");
            return $response;
        } catch (\Exception $e) {
            Log::error("Error enviando WhatsApp texto: " . $e->getMessage());
            return false;
        }
    }
}
