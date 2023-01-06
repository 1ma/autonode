<?php

declare(strict_types=1);

namespace AutoNode\Domain;

use function array_keys;
use function ksort;

final class User
{
    private string $name;
    private string $gecos;
    private bool $sudo;
    private array $groups;

    public function __construct(string $name, string $gecos, bool $sudo, array $groups)
    {
        $this->name = $name;
        $this->gecos = $gecos;
        $this->sudo = $sudo;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        $user = [
            'name' => $this->name,
            'gecos' => $this->gecos,
        ];

        if (!empty($this->groups)) {
            $user['groups'] = implode(', ', array_keys($this->groups));
        }

        if ($this->sudo) {
            $user['sudo'] = 'ALL=(ALL) NOPASSWD:ALL';
        }

        $user['shell'] = '/bin/bash';

        return $user;
    }
}
