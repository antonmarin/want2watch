<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;

require '../vendor/autoload.php';

$request = Request::createFromGlobals();
echo "hello";
