<?php

declare(strict_types=1);

namespace AutoNode\Domain\Feature;

use AutoNode\Domain\APTSource;
use AutoNode\Domain\Feature;
use AutoNode\Domain\TorHiddenService;

final class Basics implements Feature
{
    /**
     * @inheritDoc
     */
    public function getSources(): array
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

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getUsers(): array
    {
        return [];
    }

    public function extraTorHiddenServices(): array
    {
        return [
            new TorHiddenService('ssh', 22, 22)
        ];
    }

    /**
     * @inheritDoc
     */
    public function getPackages(): array
    {
        return [
            'apt-transport-https',
            'fail2ban',
            'net-tools',
            'nginx',
            'tor',
            'tree',
        ];
    }

    /**
     * @inheritDoc
     */
    public function extraAdminGroups(): array
    {
        return [
            'adm',
            'cdrom',
            'dip',
            'lxd',
            'plugdev',
            'sudo'
        ];
    }
}
