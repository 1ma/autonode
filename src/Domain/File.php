<?php

declare(strict_types=1);

namespace AutoNode\Domain;

final class File
{
    private readonly string $path;
    private readonly string $owner;
    private readonly string $permissions;
    private readonly bool $defer;
    private readonly bool $append;
    private string $content;

    public function __construct(string $path, string $owner, string $permissions, bool $defer, bool $append, string $content)
    {
        $this->path = $path;
        $this->owner = $owner;
        $this->permissions = $permissions;
        $this->defer = $defer;
        $this->append = $append;
        $this->content = $content;

        if ($this->content[-1] !== "\n") {
            $this->content .= "\n";
        }
    }

    public function appendContent(string $moreContent): void
    {
        $this->content .= "\n" . $moreContent;

        if ($this->content[-1] !== "\n") {
            $this->content .= "\n";
        }
    }

    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'owner' => $this->owner,
            'defer' => $this->defer,
            'append' => $this->append,
            'permissions' => $this->permissions,
            'content' => $this->content
        ];
    }
}
