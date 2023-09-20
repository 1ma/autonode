<?php

declare(strict_types=1);

namespace AutoNode\Handlers\GenerateTemplate;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Environment;

final class ExtraAuthenticationMethod implements RequestHandlerInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $form = $request->getParsedBody();

        $filteredForm = array_filter(
            $form,
            fn (string $value, string $key): bool => (preg_match('/^ssh\-data\-\d+$/', $key) + preg_match('/^ssh\-type\-\d+$/', $key)) && !empty($value),
            \ARRAY_FILTER_USE_BOTH
        );

        $ssh = [];
        foreach ($filteredForm as $name => $value) {
            if (preg_match('/^ssh\-type\-(\d+)$/', $name, $match)) {
                $ssh[(int) $match[1]]['type'] = $value;
            }

            if (preg_match('/^ssh\-data\-(\d+)$/', $name, $match)) {
                $ssh[(int) $match[1]]['data'] = $value;
            }
        }

        $ssh[] = ['type' => null, 'data' => null];

        return new Response(
            200,
            ['Content-Type' => 'text/html; charset=utf-8'],
            $this->twig->render('ssh_form.html.twig', ['ssh' => $ssh])
        );
    }
}
