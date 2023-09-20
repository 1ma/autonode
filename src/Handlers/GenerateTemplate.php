<?php

declare(strict_types=1);

namespace AutoNode\Handlers;

use AutoNode\Domain\CloudInitTemplate;
use AutoNode\Domain\Feature\Basics;
use AutoNode\Domain\Feature\BitcoinCore;
use AutoNode\Domain\Feature\SparrowWallet;
use AutoNode\Domain\SuperUser;
use AutoNode\Handlers\GenerateTemplate\Input;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Yaml\Yaml;

final class GenerateTemplate implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = Input::fromForm($request->getParsedBody());

        $admin = new SuperUser($input->admin);
        foreach ($input->sshKeys as $sshKey) {
            switch ($sshKey['type']) {
                case 'pk':
                    $admin->addAuthorizedKey($sshKey['data']);
                    break;
                case 'gh':
                    $admin->addImportId('gh', $sshKey['data']);
                    break;
                case 'lp':
                    $admin->addImportId('lp', $sshKey['data']);
                    break;
            }
        }

        $template = new CloudInitTemplate($input->hostname, $input->locale, $input->x86install, $admin);
        $template->add(new Basics());
        $template->add(new BitcoinCore());
        $template->add(new SparrowWallet());

        return new Response(
            200,
            ['Content-Type' => 'application/yaml', 'Content-Disposition' => 'attachment; filename="autonode.yml"'],
            "#cloud-config\n\n".Yaml::dump($template->toArray(), 6, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK)
        );
    }
}
