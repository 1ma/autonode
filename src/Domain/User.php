<?php

declare(strict_types=1);

namespace AutoNode\Domain;

use function array_keys;

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
    }

    public function addGroup(string $group): void
    {
        $this->groups[$group] = true;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGecos(): string
    {
        return $this->gecos;
    }

    public function isSudo(): bool
    {
        return $this->sudo;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        return array_keys($this->groups);
    }
}
