<?php

declare(strict_types=1);

namespace AutoNode\DI;

use AutoNode\Handlers;
use Jelly\Constants\Services;
use Jelly\Handlers\StaticResponse;
use Jelly\Jelly;
use Nyholm\Psr7\Response;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;

final class AutoNode implements ServiceProvider
{
    public function provide(Container $c): void
    {
        $c->set(AutoNode::class, static function (Container $c): Jelly {
            $c->set(Services::NOT_FOUND_HANDLER->value, new StaticResponse(new Response(404)));
            $c->set(Services::BAD_METHOD_HANDLER->value, new StaticResponse(new Response(405)));

            $app = new Jelly($c);

            $app->GET('/', Handlers\LandingPage::class);
            $app->POST('/generate', Handlers\GenerateTemplate::class);

            return $app;
        });
    }
}
