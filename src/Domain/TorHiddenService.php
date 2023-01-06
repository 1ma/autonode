<?php

declare(strict_types=1);

namespace AutoNode\Domain;

final class TorHiddenService
{
    private readonly string $name;
    private readonly int $externalPort;
    private readonly int $internalPort;

    public function __construct(string $name, int $externalPort, int $internalPort)
    {
        $this->name = $name;
        $this->externalPort = $externalPort;
        $this->internalPort = $internalPort;
    }

    public function __toString(): string
    {
        return <<<TXT
HiddenServiceDir /var/lib/tor/$this->name/
HiddenServicePort $this->externalPort 127.0.0.1:$this->internalPort
TXT;
    }
}
