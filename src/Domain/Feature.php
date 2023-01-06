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
    public function torHiddenServices(): array;

    /** @return string[] */
    public function adminGroups(): array;

    /** @return string[] */
    public function getPackages(): array;
}
