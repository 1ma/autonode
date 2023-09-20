<?php

namespace Deployer;

require 'recipe/common.php';

set('application', 'autonode');
set('repository', 'git@github.com:1ma/autonode');
set('keep_releases', 5);

host('autonode.1mahq.com')
    ->setRemoteUser('deployer')
    ->setDeployPath('~/{{application}}')
    ->setSshArguments([
        '-o StrictHostKeyChecking=accept-new',
    ]);

task('autonode:build', static function () {
    runLocally('composer install --no-dev --classmap-authoritative');
});

task('autonode:upload', static function () {
    upload(__DIR__ . '/composer.json', '{{release_path}}');
    upload(__DIR__ . '/composer.lock', '{{release_path}}');
    upload(__DIR__ . '/resources', '{{release_path}}');
    upload(__DIR__ . '/src', '{{release_path}}');
    upload(__DIR__ . '/vendor', '{{release_path}}');
    upload(__DIR__ . '/web', '{{release_path}}');
});

task('autonode:check-reqs', static function () {
    run('composer --working-dir=autonode/release check-platform-reqs --no-dev');
});

task('autonode:bust-opcache', static function () {
    run('cachetool opcache:reset');
});

task('autonode:deploy', [
    'autonode:build',

    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'deploy:shared',
    'deploy:writable',

    'autonode:upload',
    'autonode:check-reqs',

    'deploy:symlink',
    'deploy:unlock',

    'autonode:bust-opcache',

    'deploy:cleanup',
    'deploy:success',
]);
