<?php

declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Core\Router;
use App\Core\View;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$config = require dirname(__DIR__) . '/config/config.php';

$view = new View(
    $config['smarty'],
);

$homeController = new HomeController(
    $view,
);

$categoryController = new CategoryController(
    $view,
);

$router = new Router();

$router->get(
    '/',
    [$homeController, 'index'],
);

$router->get(
    '/category/{slug}',
    [$categoryController, 'show'],
);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);
