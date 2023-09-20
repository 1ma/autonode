<?php

declare(strict_types=1);

namespace AutoNode\Domain;

class SystemUser
{
    public readonly string $name;
    private readonly string $gecos;
    private array $groups;

    public function __construct(string $name, string $gecos, array $groups = [])
    {
        $this->name = $name;
        $this->gecos = $gecos;
        $this->groups = [];
        foreach ($groups as $group) {
            $this->groups[$group] = true;
        }

        ksort($this->groups);
    }

    public function addGroup(string $group): void
    {
        $this->groups[$group] = true;

        ksort($this->groups);
    }

    public function toArray(): array
    {
        $user = [
            'name' => $this->name,
            'gecos' => $this->gecos,
            'shell' => '/bin/bash',
        ];

        if (!empty($this->groups)) {
            $user['groups'] = implode(', ', array_keys($this->groups));
        }

        return $user;
    }
}
