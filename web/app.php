<?php

declare(strict_types=1);

use AutoNode\DI;
use UMA\DIC\Container;

define('__ROOT__', dirname(__DIR__));

require_once __ROOT__ . '/vendor/autoload.php';

$cnt = new Container();
$cnt->register(new DI\Handlers());
$cnt->register(new DI\AutoNode());

$cnt->get(DI\AutoNode::class)->run();
