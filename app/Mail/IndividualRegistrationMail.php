<?php
// app/Mail/IndividualRegistrationMail.php

namespace App\Mail;

use App\Models\Registration;
use App\Models\Athlete;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IndividualRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $athlete;

    public function __construct(Registration $registration, Athlete $athlete)
    {
        $this->registration = $registration;
        $this->athlete = $athlete;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Inscripción - Vuelta a la Fría 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.individual-registration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
