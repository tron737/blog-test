<?php

declare(strict_types=1);

return [
    'database' => [
        'dsn' => sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'] ?? 'mysql',
            $_ENV['DB_PORT'] ?? '3306',
            $_ENV['DB_DATABASE'] ?? 'blog',
        ),
        'username' => $_ENV['DB_USERNAME'] ?? 'default',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
    ],

    'smarty' => [
        'templates' => dirname(__DIR__) . '/templates',
        'compile' => dirname(__DIR__) . '/storage/smarty/compile',
        'cache' => dirname(__DIR__) . '/storage/smarty/cache',
    ],
];
