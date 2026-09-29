<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class CategoryRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * @return list<array{
     *     id: int,
     *     name: string,
     *     slug: string,
     *     description: string|null
     * }>
     */
    public function findAllWithPosts(): array
    {
        $statement = $this->pdo->query(
            '
            SELECT DISTINCT
                c.id,
                c.name,
                c.slug,
                c.description
            FROM categories c
            INNER JOIN post_categories pc ON pc.category_id = c.id
            INNER JOIN posts p ON p.id = pc.post_id
            ORDER BY c.name ASC
            ',
        );

        /** @var list<array{id: int, name: string, slug: string, description: string|null}> $categories */
        $categories = $statement->fetchAll();

        return $categories;
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     slug: string,
     *     description: string|null
     * }|null
     */
    public function findBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare(
            '
            SELECT
                id,
                name,
                slug,
                description
            FROM categories
            WHERE slug = :slug
            LIMIT 1
            ',
        );

        $statement->execute([
            'slug' => $slug,
        ]);

        $category = $statement->fetch();

        if ($category === false) {
            return null;
        }

        /** @var array{id: int, name: string, slug: string, description: string|null} $category */
        return $category;
    }
}
