<?php

namespace Attendance\AttendanceContext\Domain;

interface PersonRepositoryInterface
{
    public function ofId(string $id): ?Person;

    public function save(Person $person);

    /** @return Person[] */
    public function manyOfIds(array $ids): array;
}