<?php

namespace AttendanceContext\Domain;

interface ActivityRepositoryInterface
{
    public function ofId(string $id): ?Activity;

    public function save(Activity $activity);

    /** @return Activity[] */
    public function manyOfOwner(string $ownerId): array;
}