<?php

declare(strict_types=1);

namespace AutoNode\Domain;

/**
 * Implement this interface to add new software to the AutoNode template
 */
interface Feature
{
    /**
     * Additional APT sources for the OS
     *
     * @return APTSource[]
     */
    public function sources(): array;

    /**
     * Additional files to be written in the filesystem
     *
     * @return File[]
     */
    public function files(): array;

    /**
     * Additional users to be created
     *
     * @return SystemUser[]
     */
    public function users(): array;

    /**
     * Additional Tor hidden services to be created
     *
     * @return TorHiddenService[]
     */
    public function torHiddenServices(): array;

    /**
     * System groups to where the admin user will be included
     *
     * @return string[]
     */
    public function adminGroups(): array;

    /**
     * Additional packages to be installed
     *
     * @return string[]
     */
    public function packages(): array;

    /**
     * Additional lines of Bash script to be appended
     * to the privileged post-install script.
     *
     * Return null if not needed
     *
     * @return string|null
     */
    public function privilegedScript(): ?string;
}
