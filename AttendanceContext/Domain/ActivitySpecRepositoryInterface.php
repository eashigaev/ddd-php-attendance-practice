<?php

namespace Attendance\AttendanceContext\Domain;

interface ActivitySpecRepositoryInterface
{
    public function ofId(string $id): ?ActivitySpec;

    public function save(ActivitySpec $spec);

    /** @return ActivitySpec[] */
    public function manyForOwnerByCriteria(string $ownerId, ?bool $isArchived = null): array;

    /** @return ActivitySpec[] */
    public function manyFavoriteForOwner(string $ownerId): array;
}