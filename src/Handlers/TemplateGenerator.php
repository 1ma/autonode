<?php

declare(strict_types=1);

namespace AutoNode\Handlers;

use AutoNode\Domain\Template;
use AutoNode\Domain\User;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Yaml\Yaml;

final class TemplateGenerator implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $template = new Template('autonode', 'en_US.UTF-8', 'satoshi');

        return new Response(
            200,
            ['Content-Type' => 'application/yaml', 'Content-Disposition' => 'attachment; filename="autoinstall.yml"'],
            Yaml::dump($template->toArray(), 4, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK)
        );
    }
}
