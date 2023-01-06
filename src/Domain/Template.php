<?php

declare(strict_types=1);

namespace AutoNode\Domain;

final class Template
{
    private string $hostname;
    private string $locale;

    private User $adminUser;

    /** @var User[] */
    private array $systemUsers;

    /** @var File[] */
    private array $files;

    /** @var string[] */
    private array $packages;

    public function __construct(string $hostname, string $locale, string $defaultUser)
    {
        $this->hostname = $hostname;
        $this->locale = $locale;
        $this->adminUser = new User($defaultUser, 'Admin user', true, ['adm', 'cdrom', 'dip', 'lxd', 'plugdev', 'sudo']);
        $this->systemUsers = [];
        $this->files = [
            new File('/etc/tor/torrc', 'root:root', '0644', true, <<<TXT
ControlPort 9051
CookieAuthentication 1
CookieAuthFileGroupReadable 1

HiddenServiceDir /var/lib/tor/sshd/
HiddenServicePort 22 127.0.0.1:22

TXT
            ),
        ];
        $this->packages = [
            'apt-transport-https',
            'fail2ban',
            'net-tools',
            'nginx',
            'tree',
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
                $this->adminUser->toArray()
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
            'power_state' => [
                'mode' => 'reboot',
            ],
            'write_files' => [
                $this->files[0]->toArray()
            ],
        ];
    }
}
