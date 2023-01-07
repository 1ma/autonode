<?php

declare(strict_types=1);

namespace AutoNode\DI;

use AutoNode\Handlers\ExtraAuthenticationMethod;
use AutoNode\Handlers\GenerateTemplate;
use AutoNode\Handlers\LandingPage;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use UMA\DIC\Container;
use UMA\DIC\ServiceProvider;

final class Handlers implements ServiceProvider
{
    public function provide(Container $c): void
    {

        $c->set(Environment::class, static function (): Environment {
            return new Environment(new FilesystemLoader(__ROOT__ . '/tpl'), ['debug' => true]);
        });

        $c->set(LandingPage::class, static function (Container $c): RequestHandlerInterface {
            return new LandingPage(
                $c->get(Environment::class)
            );
        });

        $c->set(GenerateTemplate::class, static function (): RequestHandlerInterface {
            return new GenerateTemplate();
        });

        $c->set(ExtraAuthenticationMethod::class, static function (): RequestHandlerInterface {
            return new ExtraAuthenticationMethod();
        });
    }
}
