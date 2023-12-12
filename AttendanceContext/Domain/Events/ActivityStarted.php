<?php

namespace Attendance\AttendanceContext\Domain\Events;

use Attendance\AttendanceContext\Domain\Activity;

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