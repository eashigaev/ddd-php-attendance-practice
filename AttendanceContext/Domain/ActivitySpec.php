<?php

namespace AttendanceContext\Domain;

use Attendance\Kernel\Infra\OptimisticLockingTrait;

class ActivitySpec
{
    use OptimisticLockingTrait;

    public string $id;
    public string $ownerId;
    public string $name;
    public ?int $capacity;
    public bool $isArchived;

    public bool $isFavorite;

    /** @var string[] */
    public array $memberIds;
    public bool $isPrivate;

    public static function add(string $id, string $ownerId, string $name, ?int $capacity): static
    {
        $self = new static;
        $self->id = $id;
        $self->ownerId = $ownerId;
        $self->name = $name;
        $self->capacity = $capacity;
        $self->memberIds = [];
        $self->isPrivate = false;
        $self->isArchived = false;
        $self->isFavorite = false;
        return $self;
    }

    public static function addAgain(string $id, self $spec): static
    {
        $self = new static;
        $self->id = $id;
        $self->ownerId = $spec->ownerId;
        $self->name = $spec->name;
        $self->capacity = $spec->capacity;
        $self->memberIds = $spec->memberIds;
        $self->isPrivate = $spec->isPrivate;
        $self->isArchived = false;
        return $self;
    }

    public function change(string $name, ?int $capacity): void
    {
        assert(!$this->isArchived);

        $this->name = $name;
        $this->capacity = $capacity;
    }

    public function changeIsFavorite(bool $isFavorite): void
    {
        $this->isFavorite = $isFavorite;
    }

    public function archive(): void
    {
        assert(!$this->isArchived);

        $this->isArchived = true;
    }

    // Members

    public function changeIsPrivate(bool $isPrivate): void
    {
        $this->isPrivate = $isPrivate;
    }

    public function allowVisit(string $memberId): bool
    {
        if (!$this->isPrivate) {
            return true;
        }

        $found = $this->filterMemberIds(
            fn(string $id) => $id === $memberId
        );
        return !!$found;
    }

    public function addMember(Person $person): string
    {
        assert(!$this->isArchived);

        $found = $this->filterMemberIds(
            fn(string $id) => $id === $person->id
        );
        assert(!$found);

        $memberId = $person->id;
        $this->memberIds[] = $memberId;

        return $memberId;
    }

    public function cancelMember(string $memberId): string
    {
        assert(!$this->isArchived);

        $memberId = current($this->filterMemberIds(
            fn(string $id) => $id === $memberId
        ));
        assert($memberId);

        $this->memberIds = $this->filterMemberIds(
            fn(string $id) => $id !== $memberId
        );

        return $memberId;
    }

    //

    public function filterMemberIds(callable $callback): array
    {
        return array_filter($this->memberIds, $callback);
    }
}