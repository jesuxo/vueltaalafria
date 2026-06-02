<?php
// app/Notifications/RegistrationWhatsAppNotification.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Netflie\WhatsAppCloudApi\WhatsApp;
use Netflie\WhatsAppCloudApi\Message\Template\Component;
use Netflie\WhatsAppCloudApi\Message\Template\Language;
use Netflie\WhatsAppCloudApi\Message\Template\Template;

class RegistrationWhatsAppNotification extends Notification
{
    protected $data;
    protected $whatsapp;

    public function __construct($data)
    {
        $this->data = $data;

        // Inicializar WhatsApp Cloud API
        $this->whatsapp = new WhatsApp([
            'from_phone_number_id' => config('services.whatsapp.from_phone_number_id'),
            'access_token' => config('services.whatsapp.token'),
        ]);
    }

    public function via($notifiable)
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable)
    {
        // Determinar la plantilla según el tipo de inscripción
        $templateName = $this->data['type'] === 'team'
            ? 'team_registration_confirmation'
            : 'individual_registration_confirmation';

        // Construir el template con los parámetros
        $template = new Template($templateName, new Language('es'));

        // Agregar componentes según el tipo
        if ($this->data['type'] === 'individual') {
            $template->addComponent(
                Component::create()->addParameter($this->data['athlete_name'] ?? 'Ciclista')
            );
            $template->addComponent(
                Component::create()->addParameter($this->data['dorsal'] ?? 'Pendiente')
            );
            $template->addComponent(
                Component::create()->addParameter($this->data['event_date'] ?? '12-14 Junio 2026')
            );
        } else {
            // Para equipos
            $template->addComponent(
                Component::create()->addParameter($this->data['team_name'] ?? 'Equipo')
            );
            $template->addComponent(
                Component::create()->addParameter($this->data['access_code'] ?? '')
            );
            $template->addComponent(
                Component::create()->addParameter($this->data['athletes_count'] ?? '0')
            );
            $template->addComponent(
                Component::create()->addParameter($this->data['event_date'] ?? '12-14 Junio 2026')
            );
        }

        // Enviar el mensaje
        return $this->whatsapp->sendTemplate(
            $notifiable->routeNotificationForWhatsapp(),
            $template
        );
    }
}
