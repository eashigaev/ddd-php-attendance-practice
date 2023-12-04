<?php

namespace AttendanceContext\App\Sagas;

use AttendanceContext\App\ActivitySpecAppService;
use AttendanceContext\Domain\Events\ActivityStarted;
use AttendanceContext\Domain\Person;

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