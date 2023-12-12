<?php

namespace Attendance\AttendanceContext\App\Sagas;

use Attendance\AttendanceContext\App\ActivitySpecAppService;
use Attendance\AttendanceContext\Domain\Events\ActivityStarted;
use Attendance\AttendanceContext\Domain\Person;

readonly class ActivitySpecSaga
{
    public function __construct(
        private ActivitySpecAppService $specAppService
    )
    {
    }

    public function onActivityStarted(ActivityStarted $event): ?Person
    {
        $this->specAppService->archiveActivitySpec(
            $event->activity->specId
        );
    }
}