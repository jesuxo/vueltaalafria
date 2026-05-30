<?php
// app/Helpers/RegistrationHelper.php

namespace App\Helpers;

use App\Models\Athlete;
use App\Models\Event;

class RegistrationHelper
{
    public static function canRegister($athleteId, $eventId = null)
    {
        $eventId = $eventId ?? Event::where('is_active', true)->first()->id;

        $existing = \App\Models\AthleteEventParticipation::where('athlete_id', $athleteId)
            ->where('event_id', $eventId)
            ->exists();

        return !$existing;
    }

    public static function getAthleteEvents($athleteId)
    {
        return \App\Models\AthleteEventParticipation::where('athlete_id', $athleteId)
            ->with('event')
            ->get();
    }
}
