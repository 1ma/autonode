<?php

declare(strict_types=1);

namespace AutoNode\Domain\Feature;

use AutoNode\Domain\APTSource;
use AutoNode\Domain\Feature;
use AutoNode\Domain\File;
use AutoNode\Domain\TorHiddenService;

/**
 * Base software.
 */
final class Basics implements Feature
{
    public function sources(): array
    {
        return [
            new APTSource(
                'nginx',
                'deb https://nginx.org/packages/mainline/ubuntu jammy nginx',
                '573B FD6B 3D8F BC64 1079 A6AB ABF5 BD82 7BD9 BF62'
            ),
            new APTSource(
                'nodesource',
                'deb https://deb.nodesource.com/node_18.x jammy main',
                '9FD3 B784 BC1C 6FC3 1A8A 0A1C 1655 A0AB 6857 6280'
            ),
            new APTSource(
                'tor',
                'deb https://deb.torproject.org/torproject.org jammy main',
                'A3C4 F0F9 79CA A22C DBA8 F512 EE8C BC9E 886D DD89'
            ),
        ];
    }

    public function files(): array
    {
        return [
            new File(
                '/etc/sysctl.d/99-swap-optimizations.conf',
                'root:root',
                '0644',
                false,
                false,
                file_get_contents(__ROOT__.'/resources/basics/99-swap-optimizations.conf'),
            ),
            new File(
                '/etc/nginx/nginx.conf',
                'root:root',
                '0644',
                true,
                false,
                file_get_contents(__ROOT__.'/resources/basics/nginx.conf'),
            ),
        ];
    }

    public function users(): array
    {
        return [];
    }

    public function torHiddenServices(): array
    {
        return [
            new TorHiddenService('ssh', 22, 22),
        ];
    }

    public function packages(): array
    {
        return [
            'apt-transport-https',
            'ca-certificates',
            'curl',
            'fail2ban',
            'git',
            'net-tools',
            'nginx',
            'tor',
            'tree',
        ];
    }

    public function adminGroups(): array
    {
        return [];
    }

    public function privilegedScript(): ?string
    {
        return null;
    }
}
