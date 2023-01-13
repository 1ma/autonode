<?php

namespace Deployer;

require 'recipe/common.php';

set('application', 'autonode');
set('repository', 'git@github.com:1ma/autonode');
set('keep_releases', 5);

define('APP_ROOT', __DIR__);

host('autonode.1mahq.com')
    ->setRemoteUser('deployer')
    ->setDeployPath('~/{{application}}')
    ->setPort(2009)
    ->setSshArguments([
        '-o StrictHostKeyChecking=accept-new',
    ]);

task('autonode:build', static function () {
    runLocally('composer install --no-dev --classmap-authoritative', ['cwd' => APP_ROOT]);
});

task('autonode:upload', static function () {
    upload(APP_ROOT . '/composer.json', '{{release_path}}');
    upload(APP_ROOT . '/composer.lock', '{{release_path}}');
    upload(APP_ROOT . '/src', '{{release_path}}');
    upload(APP_ROOT . '/tpl', '{{release_path}}');
    upload(APP_ROOT . '/vendor', '{{release_path}}');
    upload(APP_ROOT . '/web', '{{release_path}}');
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
