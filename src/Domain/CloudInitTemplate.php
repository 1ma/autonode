<?php

declare(strict_types=1);

namespace AutoNode\Domain;

use function array_map;
use function array_merge;
use function array_reduce;

final class CloudInitTemplate
{
    private string $hostname;
    private string $locale;
    private User $adminUser;
    private File $torConfig;

    /** @var User[] */
    private array $systemUsers;

    /** @var File[] */
    private array $files;

    /** @var APTSource[] */
    private array $sources;

    /** @var string[] */
    private array $packages;

    public function __construct(string $hostname, string $locale, User $adminUser)
    {
        $this->hostname = $hostname;
        $this->locale = $locale;
        $this->adminUser = $adminUser;
        $this->systemUsers = [];
        $this->files = [];
        $this->sources = [];
        $this->packages = [];
        $this->torConfig = new File(
            '/etc/tor/torrc',
            'root:root',
            '0644',
            true,
            true,
            <<<TXT
ControlPort 9051
CookieAuthentication 1
CookieAuthFileGroupReadable 1
TXT
        );
    }

    public function add(Feature $feature): void
    {
        $this->systemUsers = array_merge($this->systemUsers, $feature->getUsers());
        $this->files = array_merge($this->files, $feature->getFiles());
        $this->packages = array_merge($this->packages, $feature->getPackages());
        $this->sources = array_merge($this->sources, $feature->getSources());

        // TODO sort some of these arrays

        foreach ($feature->extraAdminGroups() as $extraAdminGroup) {
            $this->adminUser->addGroup($extraAdminGroup);
        }

        foreach ($feature->extraTorHiddenServices() as $torHiddenService) {
            $this->torConfig->appendContent((string) $torHiddenService);
        }
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
                ...array_map(fn (User $u): array => $u->toArray(), $this->systemUsers),
                $this->adminUser->toArray(),
            ],
            'apt' => [
                'sources' => array_reduce(
                    $this->sources,
                    fn (array $r, APTSource $s): array => $r + [$s->getName() => $s->toArray()],
                    []
                ),
            ],
            'package_update' => true,
            'package_upgrade' => true,
            'packages' => $this->packages,
            'power_state' => [
                'mode' => 'reboot',
            ],
            'write_files' => [
                $this->torConfig->toArray(),
                ...array_map(fn (File $f): array => $f->toArray(), $this->files),
            ],
        ];
    }
}
