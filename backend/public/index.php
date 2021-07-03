<?php

declare(strict_types=1);

use Infrastructure\IoC\Symfony\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

if ($_ENV['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_ENV['APP_ENV'], (bool) $_ENV['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

require dirname(__DIR__).'/vendor/autoload.php';
