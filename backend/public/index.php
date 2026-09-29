<?php

declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Core\Database;
use App\Core\Router;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(
    dirname(__DIR__),
);

$dotenv->safeLoad();

$config = require dirname(__DIR__) . '/config/config.php';

$database = Database::getInstance(
    $config['database'],
);

$pdo = $database->connection();

$view = new View(
    $config['smarty'],
);

$categoryRepository = new CategoryRepository(
    $pdo,
);

$postRepository = new PostRepository(
    $pdo,
);

$homeController = new HomeController(
    $view,
    $categoryRepository,
    $postRepository,
);

$categoryController = new CategoryController(
    $view,
    $categoryRepository,
    $postRepository,
);

$postController = new PostController(
    $view,
    $postRepository,
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

$router->get(
    '/post/{slug}',
    [$postController, 'show'],
);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);
