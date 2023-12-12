<?php

namespace Attendance\AttendanceContext\App;

use Attendance\AttendanceContext\Domain\Activity;
use Attendance\AttendanceContext\Domain\ActivityRepositoryInterface;
use Attendance\AttendanceContext\Domain\ActivitySpecRepositoryInterface;
use Attendance\AttendanceContext\Domain\Events\ActivityStarted;
use Attendance\AttendanceContext\Domain\PersonRepositoryInterface;
use Attendance\Kernel\Infra\Messaging\MessageBusInterface;
use Attendance\Kernel\Infra\Moment\MomentInterface;

readonly class ActivityAppService
{
    public function __construct(
        private MomentInterface                 $moment,
        private ActivitySpecRepositoryInterface $specRepository,
        private ActivityRepositoryInterface     $activityRepository,
        private PersonRepositoryInterface       $personRepository,
        private MessageBusInterface             $messageBus
    )
    {
    }

    // Queries

    public function getActivity(string $activityId): ?Activity
    {
        return $this->activityRepository->ofId($activityId);
    }

    /** @return Activity[] */
    public function getOwnerActivityList(string $ownerId): array
    {
        return $this->activityRepository->manyOfOwner($ownerId);
    }

    // Commands

    public function startActivity(string $specId): string
    {
        $spec = $this->specRepository->ofId($specId);
        assert($spec);

        $momentAt = $this->moment->now();

        $activity = Activity::start(uniqid(), $spec, $momentAt);
        $this->activityRepository->save($activity);

        return $spec->id;
    }

    public function finishActivity(string $activityId): void
    {
        $activity = $this->activityRepository->ofId($activityId);
        assert($activity);

        $activity->finish();
        $this->activityRepository->save($activity);

        $this->messageBus->emit(
            ActivityStarted::from($activity)
        );
    }

    // Visitors

    public function countActivityVisitor(string $activityId, string $visitorId): void
    {
        $visitor = $this->personRepository->ofId($visitorId);
        assert($visitor);

        $activity = $this->activityRepository->ofId($activityId);
        assert($activity);

        $spec = $this->specRepository->ofId($activity->specId);
        assert($spec);

        $activity->countVisitor($visitor, $spec);
        $this->activityRepository->save($activity);
    }

    public function cancelActivityVisitor(string $activityId, string $visitorId): void
    {
        $activity = $this->activityRepository->ofId($activityId);
        assert($activity);

        $activity->cancelVisitor($visitorId);
        $this->activityRepository->save($activity);
    }
}