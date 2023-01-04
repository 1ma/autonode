<?php

declare(strict_types=1);

namespace AutoNode\DI;

use AutoNode\Handlers\TemplateGenerator;
use Jelly\Constants\Services;
use Jelly\Handlers\StaticResponse;
use Nyholm\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;

final class Handlers implements ServiceProvider
{
    public function provide(Container $c): void
    {
        $c->set(Services::NOT_FOUND_HANDLER->value, new StaticResponse(new Response(404)));
        $c->set(Services::BAD_METHOD_HANDLER->value, new StaticResponse(new Response(405)));

        $c->set(TemplateGenerator::class, static function (): RequestHandlerInterface {
            return new TemplateGenerator();
        });
    }
}
