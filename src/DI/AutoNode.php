<?php

declare(strict_types=1);

namespace AutoNode\DI;

use AutoNode\Handlers;
use Jelly\Jelly;
use Psr\Container\ContainerInterface;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;

final class AutoNode implements ServiceProvider
{
    public function provide(Container $c): void
    {
        $c->set(AutoNode::class, static function (ContainerInterface $c): Jelly {
            $app = new Jelly($c);

            $app->POST('/generate', Handlers\TemplateGenerator::class);

            return $app;
        });
    }
}
