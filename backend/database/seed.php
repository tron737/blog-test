<?php

declare(strict_types=1);

use App\Core\Database;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$config = require dirname(__DIR__) . '/config/config.php';

$pdo = Database::getInstance(
    $config['database'],
)->connection();

$categories = [
    [
        'name' => 'PHP',
        'slug' => 'php',
        'description' => 'Статьи о PHP и backend-разработке.',
    ],
    [
        'name' => 'MySQL',
        'slug' => 'mysql',
        'description' => 'Материалы о MySQL, SQL и работе с данными.',
    ],
    [
        'name' => 'Docker',
        'slug' => 'docker',
        'description' => 'Контейнеризация и окружение разработки.',
    ],
    [
        'name' => 'Backend',
        'slug' => 'backend',
        'description' => 'Архитектура и практика backend-разработки.',
    ],
    [
        'name' => 'DevOps',
        'slug' => 'devops',
        'description' => 'Инфраструктура, CI/CD и автоматизация.',
    ],
];

$postTitles = [
    'Работа с PDO в PHP',
    'Типизация в PHP 8',
    'Принципы SOLID на практике',
    'Как работает Composer',
    'Маршрутизация без фреймворка',
    'Основы MySQL индексов',
    'JOIN в SQL',
    'Оптимизация SQL-запросов',
    'Транзакции в MySQL',
    'Нормализация базы данных',
    'Docker для PHP проекта',
    'Docker Compose для локальной разработки',
    'Как работает Nginx',
    'PHP-FPM простыми словами',
    'Переменные окружения в Docker',
    'Что такое Repository',
    'MVC на чистом PHP',
    'Dependency Injection без контейнера',
    'Работа с шаблонизатором Smarty',
    'Пагинация на PHP',
    'Сортировка данных в SQL',
    'Защита от SQL Injection',
    'Prepared Statements в PDO',
    'Что такое CI/CD',
    'GitHub Actions для PHP',
    'Логи и диагностика приложения',
    'HTTP статус-коды',
    'REST API основы',
    'Кэширование в backend',
    'Ошибки и исключения в PHP',
    'PHPStan в реальном проекте',
    'PHP CS Fixer и PSR-12',
    'Проектирование таблиц MySQL',
    'Foreign Key и каскадное удаление',
    'Many-to-many связи',
    'Как устроен HTTP запрос',
];

$pdo->beginTransaction();

try {
    $pdo->exec('DELETE FROM post_categories');
    $pdo->exec('DELETE FROM posts');
    $pdo->exec('DELETE FROM categories');

    $categoryStatement = $pdo->prepare(
        '
        INSERT INTO categories (
            name,
            slug,
            description
        ) VALUES (
            :name,
            :slug,
            :description
        )
        '
    );

    foreach ($categories as $category) {
        $categoryStatement->execute($category);
    }

    $categoryRows = $pdo
        ->query('SELECT id, slug FROM categories')
        ->fetchAll();

    /** @var array<string, int> $categoryIds */
    $categoryIds = [];

    foreach ($categoryRows as $categoryRow) {
        $categoryIds[$categoryRow['slug']] = (int) $categoryRow['id'];
    }

    $postStatement = $pdo->prepare(
        '
        INSERT INTO posts (
            image,
            title,
            slug,
            description,
            content,
            views,
            published_at
        ) VALUES (
            :image,
            :title,
            :slug,
            :description,
            :content,
            :views,
            :published_at
        )
        '
    );

    $relationStatement = $pdo->prepare(
        '
        INSERT INTO post_categories (
            post_id,
            category_id
        ) VALUES (
            :post_id,
            :category_id
        )
        '
    );

    $categorySlugs = array_keys($categoryIds);

    foreach ($postTitles as $index => $title) {
        $number = $index + 1;

        $slug = sprintf(
            'post-%02d',
            $number,
        );

        $publishedAt = (new DateTimeImmutable())
            ->modify(sprintf('-%d days', count($postTitles) - $number))
            ->setTime(
                ($number % 12) + 8,
                ($number * 7) % 60,
            );

        $views = ($number * 17) % 250;

        $postStatement->execute([
            'image' => sprintf(
                '/images/post-%d.jpg',
                (($number - 1) % 6) + 1,
            ),
            'title' => $title,
            'slug' => $slug,
            'description' => sprintf(
                'Краткое описание статьи «%s».',
                $title,
            ),
            'content' => sprintf(
                "Это демонстрационный текст статьи «%s».\n\n"
                . "Здесь может находиться основной контент публикации.\n\n"
                . "Материал создан сидером для проверки работы блога.",
                $title,
            ),
            'views' => $views,
            'published_at' => $publishedAt->format('Y-m-d H:i:s'),
        ]);

        $postId = (int) $pdo->lastInsertId();

        $primaryCategory = $categorySlugs[
        $index % count($categorySlugs)
        ];

        $relationStatement->execute([
            'post_id' => $postId,
            'category_id' => $categoryIds[$primaryCategory],
        ]);

        if ($number % 3 === 0) {
            $secondaryCategory = $categorySlugs[
            ($index + 1) % count($categorySlugs)
            ];

            $relationStatement->execute([
                'post_id' => $postId,
                'category_id' => $categoryIds[$secondaryCategory],
            ]);
        }

        if ($number % 7 === 0) {
            $thirdCategory = $categorySlugs[
            ($index + 2) % count($categorySlugs)
            ];

            $relationStatement->execute([
                'post_id' => $postId,
                'category_id' => $categoryIds[$thirdCategory],
            ]);
        }
    }

    $pdo->commit();

    echo "Database seeded successfully.\n";
} catch (Throwable $exception) {
    $pdo->rollBack();

    throw $exception;
}
