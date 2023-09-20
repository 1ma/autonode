<?php

declare(strict_types=1);

namespace AutoNode\Domain;

final class CloudInitTemplate
{
    private string $hostname;
    private string $locale;
    private bool $x86install;
    private SuperUser $admin;
    private File $torConfig;
    private File $privilegedSetup;

    /** @var SystemUser[] */
    private array $users;

    /** @var File[] */
    private array $files;

    /** @var APTSource[] */
    private array $sources;

    /** @var string[] */
    private array $packages;

    public function __construct(string $hostname, string $locale, bool $x86install, SuperUser $admin)
    {
        $this->hostname = $hostname;
        $this->locale = $locale;
        $this->x86install = $x86install;
        $this->admin = $admin;
        $this->users = [];
        $this->files = [];
        $this->sources = [];
        $this->packages = [];
        $this->torConfig = new File(
            '/etc/tor/torrc',
            'root:root',
            '0644',
            true,
            true,
            file_get_contents(__ROOT__.'/resources/basics/torrc')
        );
        $this->privilegedSetup = new File(
            "/home/{$this->admin->name}/superuser-setup.sh",
            "{$this->admin->name}:{$this->admin->name}",
            '0755',
            true,
            false,
            file_get_contents(__ROOT__.'/resources/basics/superuser-setup.sh'),
        );
    }

    public function add(Feature $feature): void
    {
        $this->users = array_merge($this->users, $feature->users());
        $this->files = array_merge($this->files, $feature->files());
        $this->packages = array_merge($this->packages, $feature->packages());
        $this->sources = array_merge($this->sources, $feature->sources());

        // TODO sort some of these arrays

        foreach ($feature->adminGroups() as $adminGroup) {
            $this->admin->addGroup($adminGroup);
        }

        foreach ($feature->torHiddenServices() as $torHiddenService) {
            $this->torConfig->appendContent((string) $torHiddenService);
        }

        if (\is_string($feature->privilegedScript())) {
            $this->privilegedSetup->appendContent($feature->privilegedScript());
        }
    }

    public function toArray(): array
    {
        $template = [
            'hostname' => $this->hostname,
            'locale' => $this->locale,
            'system_info' => [
                'default_user' => [
                    'name' => $this->admin->name,
                ],
            ],
            'users' => [
                ...array_map(fn (SystemUser $u): array => $u->toArray(), $this->users),
                $this->admin->toArray(),
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
                $this->privilegedSetup->toArray(),
            ],
        ];

        if ($this->x86install) {
            $template = [
                'autoinstall' => [
                    'version' => 1,
                    'interactive-sections' => [
                        'refresh-installer',
                        'keyboard',
                        'network',
                        'proxy',
                        'apt',
                        'storage',
                        'drivers',
                    ],
                    'ssh' => [
                        'install-server' => true,
                        'allow-pw' => false,
                    ],
                    'user-data' => $template,
                ],
            ];
        }

        return $template;
    }
}
