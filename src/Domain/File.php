<?php

declare(strict_types=1);

namespace AutoNode\Domain;

final class File
{
    private string $path;
    private string $owner;
    private string $permissions;
    private string $content;
    private bool $defer;

    public function __construct(string $path, string $owner, string $permissions, bool $defer, string $content)
    {
        $this->path = $path;
        $this->owner = $owner;
        $this->permissions = $permissions;
        $this->content = $content;
        $this->defer = $defer;
    }

    public function appendContent(string $moreContent): void
    {
        $this->content .= $moreContent;
    }

    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'owner' => $this->owner,
            'defer' => $this->defer ? 'true' : 'false',
            'permissions' => $this->permissions,
            'content' => $this->content
        ];
    }
}
