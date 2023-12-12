<?php

namespace Attendance\AttendanceContext\Domain;

use Attendance\Kernel\Infra\OptimisticLockingTrait;
use DateTime;
use Exception;

class Activity
{
    use OptimisticLockingTrait;

    public string $id;
    public string $ownerId;
    public string $specId;
    public DateTime $momentAt;
    public bool $isFinished;

    /** @var string[] */
    public array $visitorIds;

    public static function start(string $id, ActivitySpec $spec, DateTime $momentAt): static
    {
        assert(!$spec->isArchived);

        $self = new static;
        $self->id = $id;
        $self->ownerId = $spec->ownerId;
        $self->specId = $spec->id;
        $self->momentAt = $momentAt;
        $self->isFinished = false;
        $self->visitorIds = [];
        return $self;
    }

    public function finish(): void
    {
        assert(!$this->isFinished);

        $this->isFinished = true;
    }

    // Visits

    public function countVisitor(Person $visitor, ActivitySpec $spec): string
    {
        assert(!$this->isFinished);
        assert($this->specId === $spec->id);

        if (count($this->visitorIds) >= $spec->capacity) {
            throw new Exception('Activity capacity reached');
        }

        if (!$spec->allowVisit($visitor->id)) {
            throw new Exception('Activity requires private membership');
        }

        $found = $this->filterVisitorIds(
            fn(string $id) => $id === $visitor->id
        );
        assert(!$found);

        $visitorId = $visitor->id;
        $this->visitorIds[] = $visitorId;

        return $visitorId;
    }

    public function cancelVisitor(string $visitorId): string
    {
        assert(!$this->isFinished);

        $visit = current($this->filterVisitorIds(
            fn(string $id) => $id === $visitorId
        ));
        assert($visit);

        $this->visitorIds = $this->filterVisitorIds(
            fn(string $id) => $id !== $visitorId
        );

        return $visit;
    }

    //

    public function filterVisitorIds(callable $callback): array
    {
        return array_filter($this->visitorIds, $callback);
    }
}