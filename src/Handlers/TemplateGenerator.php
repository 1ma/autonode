<?php

declare(strict_types=1);

namespace AutoNode\Handlers;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function file_get_contents;

final class TemplateGenerator implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(
            200,
            ['Content-Type' => 'application/yaml', 'Content-Disposition' => 'attachment; filename="autonode.yml"'],
            file_get_contents(__ROOT__ . '/config.yml')
        );
    }
}
