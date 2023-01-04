<?php

declare(strict_types=1);

namespace AutoNode\DI;

use AutoNode\Handlers\TemplateGenerator;
use Psr\Http\Server\RequestHandlerInterface;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;

final class Handlers implements ServiceProvider
{
    public function provide(Container $c): void
    {
        $c->set(TemplateGenerator::class, static function (): RequestHandlerInterface {
            return new TemplateGenerator();
        });
    }
}
