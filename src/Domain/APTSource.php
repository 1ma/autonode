<?php

declare(strict_types=1);

namespace AutoNode\Domain;

final class APTSource
{
    private readonly string $name;
    private readonly string $source;
    private readonly ?string $keyId;

    public function __construct(string $name, string $source, ?string $keyId)
    {
        $this->name = $name;
        $this->source = $source;
        $this->keyId = $keyId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        $aptSource = [
            'source' => $this->source,
        ];

        if (null !== $this->keyId) {
            $aptSource['keyid'] = $this->keyId;
        }

        return $aptSource;
    }
}
