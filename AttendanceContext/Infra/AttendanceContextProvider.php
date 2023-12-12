<?php

namespace Attendance\AttendanceContext\Infra;

use Attendance\AttendanceContext\App\Sagas\ActivitySpecSaga;
use Attendance\AttendanceContext\Domain\Events\ActivityStarted;
use Attendance\Kernel\Infra\Messaging\MessageBusInterface;

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