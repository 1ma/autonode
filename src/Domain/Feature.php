<?php

declare(strict_types=1);

namespace AutoNode\Domain;

interface Feature
{
    /** @return APTSource[] */
    public function getSources(): array;

    /** @return File[] */
    public function getFiles(): array;

    /** @return User[] */
    public function getUsers(): array;

    /** @return TorHiddenService[] */
    public function extraTorHiddenServices(): array;

    /** @return string[] */
    public function extraAdminGroups(): array;

    /** @return string[] */
    public function getPackages(): array;
}
