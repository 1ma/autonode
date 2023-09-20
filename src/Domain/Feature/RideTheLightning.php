<?php

declare(strict_types=1);

namespace AutoNode\Domain\Feature;

use AutoNode\Domain\Feature;

final class RideTheLightning implements Feature
{
    public function sources(): array
    {
        return [];
    }

    public function files(): array
    {
        return [];
    }

    public function users(): array
    {
        return [];
    }

    public function torHiddenServices(): array
    {
        return [];
    }

    public function adminGroups(): array
    {
        return [];
    }

    public function packages(): array
    {
        return [];
    }

    public function privilegedScript(): ?string
    {
        return null;
    }
}
