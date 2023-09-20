<?php

declare(strict_types=1);

namespace AutoNode\Domain;

final class SuperUser extends SystemUser
{
    private array $sshAuthorizedKeys;
    private array $sshImportIds;

    public function __construct(string $name)
    {
        $this->sshAuthorizedKeys = [];
        $this->sshImportIds = [];

        parent::__construct($name, 'Node operator account', ['adm', 'cdrom', 'dip', 'lxd', 'plugdev', 'sudo']);
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

    public function toArray(): array
    {
        $user = parent::toArray();

        $user['sudo'] = 'ALL=(ALL) NOPASSWD:ALL';

        if (!empty($this->sshAuthorizedKeys)) {
            $user['ssh_authorized_keys'] = array_keys($this->sshAuthorizedKeys);
        }

        if (!empty($this->sshImportIds)) {
            $user['ssh_import_id'] = array_keys($this->sshImportIds);
        }

        return $user;
    }
}
