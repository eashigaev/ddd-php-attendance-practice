<?php

namespace AttendanceContext\Domain\Events;

use AttendanceContext\Domain\Activity;

class ActivityStarted
{
    public Activity $activity;

    public static function from(Activity $activity): static
    {
        $self = new static;
        $self->activity = $activity;
        return $self;
    }
}