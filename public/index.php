<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use Slim\App;
use UMA\DIC\Container;
use App\DI\Slim;
use App\DI\Doctrine;

/** @var Container $cnt */
$cnt = require_once __DIR__ . '/../bootstrap.php';

$cnt->register(new Doctrine());
$cnt->register(new Slim());

$app = $cnt->get(App::class);
$app->run();
