<?php

declare(strict_types=1);

namespace AutoNode\Domain\Feature;

use AutoNode\Domain\Feature;
use AutoNode\Domain\File;
use AutoNode\Domain\SystemUser;

final class BitcoinCore implements Feature
{
    public function sources(): array
    {
        return [];
    }

    public function files(): array
    {
        return [
            new File(
                '/home/bitcoin/bitcoin-setup.sh',
                'bitcoin:bitcoin',
                '0755',
                true,
                false,
                file_get_contents(__ROOT__.'/resources/bitcoin-core/bitcoin-setup.sh'),
            ),
            new File(
                '/etc/systemd/system/bitcoin.service',
                'root:root',
                '0644',
                false,
                false,
                file_get_contents(__ROOT__.'/resources/bitcoin-core/bitcoin.service'),
            ),
            new File(
                '/home/bitcoin/bitcoin.conf',
                'bitcoin:bitcoin',
                '0640',
                true,
                false,
                file_get_contents(__ROOT__.'/resources/bitcoin-core/bitcoin.conf'),
            ),
        ];
    }

    public function users(): array
    {
        return [
            new SystemUser('bitcoin', 'bitcoin system user'),
        ];
    }

    public function torHiddenServices(): array
    {
        return [];
    }

    public function adminGroups(): array
    {
        return [
            'bitcoin',
        ];
    }

    public function packages(): array
    {
        return [
            'autoconf',
            'automake',
            'build-essential',
            'libtool',
            'pkg-config',
        ];
    }

    public function privilegedScript(): ?string
    {
        return file_get_contents(__ROOT__.'/resources/bitcoin-core/bitcoin-setup-privileged.sh');
    }
}
