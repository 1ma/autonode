<?php

declare(strict_types=1);

namespace AutoNode\Domain;

use function array_keys;
use function ksort;

final class User
{
    private readonly string $name;
    private readonly string $gecos;
    private readonly bool $sudo;
    private array $groups;
    private array $sshAuthorizedKeys;
    private array $sshImportIds;

    public function __construct(string $name, string $gecos, bool $sudo, array $groups = [])
    {
        $this->name = $name;
        $this->gecos = $gecos;
        $this->sudo = $sudo;
        $this->groups = [];
        foreach ($groups as $group) {
            $this->groups[$group] = true;
        }

        ksort($this->groups);

        $this->sshAuthorizedKeys = [];
        $this->sshImportIds = [];
    }

    public function addGroup(string $group): void
    {
        $this->groups[$group] = true;

        ksort($this->groups);
    }

    public function addAuthorizedKey(string $publicKey): void
    {
        $this->sshAuthorizedKeys[$publicKey] = true;

        ksort($this->sshAuthorizedKeys);
    }

    public function addImportId(string $source, string $username): void
    {
        $this->sshImportIds["$source:$username"] = true;

        ksort($this->sshImportIds);
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
            'shell' => '/bin/bash',
        ];

        if (!empty($this->groups)) {
            $user['groups'] = implode(', ', array_keys($this->groups));
        }

        if (!empty($this->sshAuthorizedKeys)) {
            $user['ssh_authorized_keys'] = array_keys($this->sshAuthorizedKeys);
        }

        if (!empty($this->sshImportIds)) {
            $user['ssh_import_id'] = array_keys($this->sshImportIds);
        }

        if ($this->sudo) {
            $user['sudo'] = 'ALL=(ALL) NOPASSWD:ALL';
        }

        return $user;
    }
}
