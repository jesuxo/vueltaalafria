<?php
// app/Mail/TeamRegistrationMail.php

namespace App\Mail;

use App\Models\Registration;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $registration;
    public $team;
    public $athleteCount;
    public $staffCount;

    public function __construct(Registration $registration, Team $team, $athleteCount, $staffCount)
    {
        $this->registration = $registration;
        $this->team = $team;
        $this->athleteCount = $athleteCount;
        $this->staffCount = $staffCount;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Inscripción de Equipo - Vuelta a la Fría 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-registration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
