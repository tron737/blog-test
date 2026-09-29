<?php

declare(strict_types=1);

use App\Core\Database;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$config = require dirname(__DIR__) . '/config/config.php';

$pdo = Database::getInstance($config['database'])->connection();

$pdo->beginTransaction();

try {
    $pdo->exec('DELETE FROM post_categories');
    $pdo->exec('DELETE FROM posts');
    $pdo->exec('DELETE FROM categories');

    $categories = [
        [
            'name' => 'PHP',
            'slug' => 'php',
            'description' => 'Статьи о PHP и backend-разработке.',
        ],
        [
            'name' => 'MySQL',
            'slug' => 'mysql',
            'description' => 'Материалы о базах данных и SQL.',
        ],
        [
            'name' => 'Docker',
            'slug' => 'docker',
            'description' => 'Статьи о контейнеризации и окружении.',
        ],
    ];

    $categoryStatement = $pdo->prepare(
        'INSERT INTO categories (name, slug, description)
         VALUES (:name, :slug, :description)',
    );

    foreach ($categories as $category) {
        $categoryStatement->execute($category);
    }

    $posts = [
        [
            'title' => 'Работа с PDO в PHP',
            'slug' => 'pdo-in-php',
            'description' => 'Основы работы с PDO.',
            'content' => 'Подробный текст статьи о PDO.',
            'image' => '/images/pdo.jpg',
            'views' => 25,
            'published_at' => '2026-09-20 10:00:00',
        ],
        [
            'title' => 'Индексы в MySQL',
            'slug' => 'mysql-indexes',
            'description' => 'Как работают индексы.',
            'content' => 'Подробный текст статьи об индексах.',
            'image' => '/images/mysql.jpg',
            'views' => 80,
            'published_at' => '2026-09-22 12:00:00',
        ],
        [
            'title' => 'Docker для PHP-приложения',
            'slug' => 'docker-for-php',
            'description' => 'Как поднять PHP в Docker.',
            'content' => 'Подробный текст статьи о Docker.',
            'image' => '/images/docker.jpg',
            'views' => 40,
            'published_at' => '2026-09-25 15:00:00',
        ],
    ];

    $postStatement = $pdo->prepare(
        'INSERT INTO posts (
            title,
            slug,
            description,
            content,
            image,
            views,
            published_at
        ) VALUES (
            :title,
            :slug,
            :description,
            :content,
            :image,
            :views,
            :published_at
        )',
    );

    foreach ($posts as $post) {
        $postStatement->execute($post);
    }

    $pdo->exec(
        'INSERT INTO post_categories (post_id, category_id)
         VALUES
            (1, 1),
            (2, 2),
            (3, 1),
            (3, 3)',
    );

    $pdo->commit();

    echo "Database seeded successfully.\n";
} catch (Throwable $exception) {
    $pdo->rollBack();

    throw $exception;
}
