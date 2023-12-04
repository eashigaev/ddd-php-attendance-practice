<?php

namespace AttendanceContext\Infra;

use Attendance\Kernel\Infra\Messaging\MessageBusInterface;
use AttendanceContext\App\Sagas\ActivitySpecSaga;
use AttendanceContext\Domain\Events\ActivityStarted;

readonly class AttendanceContextProvider
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    )
    {
    }

    public function bootstrap(): string
    {
        $this->messageBus->listen(
            ActivityStarted::class, ActivitySpecSaga::class
        );
    }
}