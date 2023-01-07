<?php

declare(strict_types=1);

namespace AutoNode\Handlers\GenerateTemplate;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig\Environment;
use function array_filter;
use function preg_match;

final class ExtraSAN implements RequestHandlerInterface
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
            fn(string $value, string $key): bool => (preg_match('/^san\-data\-\d+$/', $key) + preg_match('/^san\-type\-\d+$/', $key)) && !empty($value),
            ARRAY_FILTER_USE_BOTH
        );

        $san = [];
        foreach ($filteredForm as $name => $value) {
                if (preg_match('/^san\-type\-(\d+)$/', $name, $match)) {
                    $san[(int) $match[1]]['type'] = $value;
                }

                if (preg_match('/^san\-data\-(\d+)$/', $name, $match)) {
                    $san[(int) $match[1]]['data'] = $value;
                }
        }

        $san[] = ['type' => null, 'data' => null];

        return new Response(
            200,
            ['Content-Type' => 'text/html; charset=utf-8'],
            $this->twig->render('san_form.html.twig', ['san' => $san])
        );
    }
}
