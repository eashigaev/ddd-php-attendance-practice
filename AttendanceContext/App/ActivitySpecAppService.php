<?php

namespace Attendance\AttendanceContext\App;

use Attendance\AttendanceContext\Domain\ActivitySpec;
use Attendance\AttendanceContext\Domain\ActivitySpecRepositoryInterface;
use Attendance\AttendanceContext\Domain\PersonRepositoryInterface;

readonly class ActivitySpecAppService
{
    public function __construct(
        private PersonRepositoryInterface       $personRepository,
        private ActivitySpecRepositoryInterface $specRepository
    )
    {
    }

    // Queries

    public function getActivitySpec(string $specId): ?ActivitySpec
    {
        return $this->specRepository->ofId($specId);
    }

    /** @return ActivitySpec[] */
    public function getOwnerActivitySpecList(string $ownerId, array $criteria): array
    {
        return $this->specRepository->manyForOwnerByCriteria(
            $ownerId, $criteria['is_archived'] ?? null,
        );
    }

    /** @return ActivitySpec[] */
    public function getOwnerFavoriteActivitySpecList(string $ownerId, array $criteria): array
    {
        return $this->specRepository->manyFavoriteForOwner($ownerId);
    }

    // Commands

    public function addActivitySpec(string $name, string $ownerId, ?int $capacity): string
    {
        $owner = $this->personRepository->ofId($ownerId);
        assert($owner);

        $spec = ActivitySpec::add(uniqid(), $ownerId, $name, $capacity);
        $this->specRepository->save($spec);

        return $spec->id;
    }

    public function addActivitySpecAgain(string $repeatSpecId): string
    {
        $baseSpec = $this->specRepository->ofId($repeatSpecId);
        assert($baseSpec);

        $spec = ActivitySpec::addAgain(uniqid(), $baseSpec);
        $this->specRepository->save($spec);

        return $spec->id;
    }

    public function changeActivitySpec(string $specId, string $name, int $capacity): void
    {
        $spec = $this->specRepository->ofId($specId);
        assert($spec);

        $spec->change($name, $capacity);
        $this->specRepository->save($spec);
    }

    public function changeActivitySpecIsFavorite(string $specId, bool $isFavorite): void
    {
        $spec = $this->specRepository->ofId($specId);
        assert($spec);

        $spec->changeIsFavorite($isFavorite);
        $this->specRepository->save($spec);
    }

    public function archiveActivitySpec(string $specId): void
    {
        $spec = $this->specRepository->ofId($specId);
        assert($spec);

        $spec->archive();
        $this->specRepository->save($spec);
    }

    // Members

    public function addActivitySpecMember(string $specId, string $memberId): void
    {
        $member = $this->personRepository->ofId($memberId);
        assert($member);

        $spec = $this->specRepository->ofId($specId);
        assert($spec);

        $spec->addMember($member);
        $this->specRepository->save($spec);
    }

    public function cancelActivitySpecMember(string $specId, string $memberId): void
    {
        $spec = $this->specRepository->ofId($specId);
        assert($spec);

        $spec->cancelMember($memberId);
        $this->specRepository->save($spec);
    }
}