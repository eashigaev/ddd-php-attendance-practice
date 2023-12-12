<?php

namespace Attendance\AttendanceContext\Domain;

class Person
{
    public string $id;
    public string $name;

    public static function add(string $id, string $name): static
    {
        $self = new static;
        $self->id = $id;
        $self->name = $name;
        return $self;
    }

    public function change(string $name): void
    {
        $this->name = $name;
    }
}