<?php

namespace Attendance\Kernel\Infra\Moment;

use DateTime;

interface MomentInterface
{
    public function now(): DateTime;
}