<?php

declare(strict_types=1);

namespace AutoNode\Handlers;

use AutoNode\Domain\CloudInitTemplate;
use AutoNode\Domain\Feature\Basics;
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
        $admin->addAuthorizedKey('ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIKwg+bJZ7RVMbwslBzMlN2+Hfe13fCD8u2IxAZZoHeQ5 root@d2db98313a74');
        $admin->addImportId('gh', '1ma');

        $template = new CloudInitTemplate($input->hostname, $input->locale, true, $admin);
        $template->add(new Basics());

        return new Response(
            200,
            ['Content-Type' => 'application/yaml', 'Content-Disposition' => 'attachment; filename="autoinstall.yml"'],
            "#cloud-config\n\n".Yaml::dump($template->toArray(), 6, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK)
        );
    }
}
