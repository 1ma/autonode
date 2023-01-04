<?php

declare(strict_types=1);

namespace AutoNode\Domain;

use function implode;

final class Template
{
    private string $hostname;
    private string $locale;
    private User $adminUser;

    /** @var User[] */
    private array $systemUsers;

    /** @var string[] */
    private array $packages;

    public function __construct(
        string $hostname,
        string $locale,
        User $defaultUser
    )
    {
        $this->hostname = $hostname;
        $this->locale = $locale;
        $this->adminUser = $defaultUser;
        $this->systemUsers = [];
        $this->packages = [
            'apt-transport-https',
            'fail2ban',
            'net-tools',
            'nginx',
            'tree',
            'ufw',
        ];
    }

    public function toArray(): array
    {
        return [
            'hostname' => $this->hostname,
            'locale' => $this->locale,
            'system_info' => [
                'default_user' => [
                    'name' => $this->adminUser->getName()
                ]
            ],
            'users' => [
                [
                    'name' => $this->adminUser->getName(),
                    'gecos' => $this->adminUser->getGecos(),
                    'groups' => implode(', ', $this->adminUser->getGroups()),
                    'shell' => '/bin/bash',
                    'sudo' => 'ALL=(ALL) NOPASSWD:ALL',
                ]
            ],
            'apt' => [
                'sources' => [
                    'nginx' => [
                        'source' => 'deb https://nginx.org/packages/mainline/ubuntu jammy nginx',
                        'keyid' => '573B FD6B 3D8F BC64 1079 A6AB ABF5 BD82 7BD9 BF62'
                    ]
                ]
            ],
            'package_update' => true,
            'package_upgrade' => true,
            'packages' => $this->packages,
        ];
    }
}
