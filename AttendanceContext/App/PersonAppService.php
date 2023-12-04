<?php

namespace AttendanceContext\App;

use AttendanceContext\Domain\Person;
use AttendanceContext\Domain\PersonRepositoryInterface;

readonly class PersonAppService
{
    public function __construct(
        private PersonRepositoryInterface $personRepository
    )
    {
    }

    // Queries

    public function getPerson(string $personId): ?Person
    {
        return $this->personRepository->ofId($personId);
    }

    /** @return Person[] */
    public function getPersonList(array $ids): array
    {
        return $this->personRepository->manyOfIds($ids);
    }

    // Commands

    public function addPerson(string $name): string
    {
        $person = Person::add(uniqid(), $name);
        $this->personRepository->save($person);

        return $person->id;
    }

    public function changePerson(string $personId, string $name): void
    {
        $person = $this->personRepository->ofId($personId);
        assert($person);

        $person->change($name);
        $this->personRepository->save($person);
    }
}