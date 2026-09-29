<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?Database $instance = null;

    private PDO $connection;

    /**
     * @param array{
     *     dsn: string,
     *     username: string,
     *     password: string
     * } $config
     */
    private function __construct(array $config)
    {
        try {
            $this->connection = new PDO(
                $config['dsn'],
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            );
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Database connection failed.',
                0,
                $exception,
            );
        }
    }

    private function __clone(): void
    {
    }

    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize singleton.');
    }

    /**
     * @param array{
     *     dsn: string,
     *     username: string,
     *     password: string
     * } $config
     */
    public static function getInstance(array $config): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public function connection(): PDO
    {
        return $this->connection;
    }
}
