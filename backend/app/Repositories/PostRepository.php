<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class PostRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    /**
     * @return list<array{
     *     id: int,
     *     image: string|null,
     *     title: string,
     *     slug: string,
     *     description: string,
     *     views: int,
     *     published_at: string
     * }>
     */
    public function findLatestByCategory(
        int $categoryId,
        int $limit = 3,
    ): array {
        $statement = $this->pdo->prepare(
            '
            SELECT
                p.id,
                p.image,
                p.title,
                p.slug,
                p.description,
                p.views,
                p.published_at
            FROM posts p
            INNER JOIN post_categories pc ON pc.post_id = p.id
            WHERE pc.category_id = :category_id
            ORDER BY p.published_at DESC
            LIMIT :limit
            ',
        );

        $statement->bindValue(
            ':category_id',
            $categoryId,
            PDO::PARAM_INT,
        );

        $statement->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT,
        );

        $statement->execute();

        /** @var list<array{
         *     id: int,
         *     image: string|null,
         *     title: string,
         *     slug: string,
         *     description: string,
         *     views: int,
         *     published_at: string
         * }> $posts
         */
        $posts = $statement->fetchAll();

        return $posts;
    }

    /**
     * @return list<array{
     *     id: int,
     *     image: string|null,
     *     title: string,
     *     slug: string,
     *     description: string,
     *     views: int,
     *     published_at: string
     * }>
     */
    public function findByCategory(
        int $categoryId,
        string $sort,
        int $limit,
        int $offset,
    ): array {
        $orderBy = match ($sort) {
            'views' => 'p.views DESC',
            default => 'p.published_at DESC',
        };

        $statement = $this->pdo->prepare(
            "
            SELECT
                p.id,
                p.image,
                p.title,
                p.slug,
                p.description,
                p.views,
                p.published_at
            FROM posts p
            INNER JOIN post_categories pc ON pc.post_id = p.id
            WHERE pc.category_id = :category_id
            ORDER BY {$orderBy}
            LIMIT :limit
            OFFSET :offset
            ",
        );

        $statement->bindValue(
            ':category_id',
            $categoryId,
            PDO::PARAM_INT,
        );

        $statement->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT,
        );

        $statement->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT,
        );

        $statement->execute();

        /** @var list<array{
         *     id: int,
         *     image: string|null,
         *     title: string,
         *     slug: string,
         *     description: string,
         *     views: int,
         *     published_at: string
         * }> $posts
         */
        $posts = $statement->fetchAll();

        return $posts;
    }

    public function countByCategory(int $categoryId): int
    {
        $statement = $this->pdo->prepare(
            '
            SELECT COUNT(*)
            FROM post_categories
            WHERE category_id = :category_id
            ',
        );

        $statement->execute([
            'category_id' => $categoryId,
        ]);

        return (int) $statement->fetchColumn();
    }

    /**
     * @return array{
     *     id: int,
     *     image: string|null,
     *     title: string,
     *     slug: string,
     *     description: string,
     *     content: string,
     *     views: int,
     *     published_at: string
     * }|null
     */
    public function findBySlug(string $slug): ?array
    {
        $statement = $this->pdo->prepare(
            '
            SELECT
                id,
                image,
                title,
                slug,
                description,
                content,
                views,
                published_at
            FROM posts
            WHERE slug = :slug
            LIMIT 1
            ',
        );

        $statement->execute([
            'slug' => $slug,
        ]);

        $post = $statement->fetch();

        if ($post === false) {
            return null;
        }

        /** @var array{
         *     id: int,
         *     image: string|null,
         *     title: string,
         *     slug: string,
         *     description: string,
         *     content: string,
         *     views: int,
         *     published_at: string
         * } $post
         */
        return $post;
    }

    public function incrementViews(int $postId): void
    {
        $statement = $this->pdo->prepare(
            '
            UPDATE posts
            SET views = views + 1
            WHERE id = :id
            ',
        );

        $statement->execute([
            'id' => $postId,
        ]);
    }

    /**
     * @return list<int>
     */
    public function findCategoryIdsByPost(int $postId): array
    {
        $statement = $this->pdo->prepare(
            '
            SELECT category_id
            FROM post_categories
            WHERE post_id = :post_id
            ',
        );

        $statement->execute([
            'post_id' => $postId,
        ]);

        /** @var list<int|string> $categoryIds */
        $categoryIds = $statement->fetchAll(
            PDO::FETCH_COLUMN,
        );

        return array_map(
            static fn (int|string $id): int => (int) $id,
            $categoryIds,
        );
    }

    /**
     * @param list<int> $categoryIds
     *
     * @return list<array{
     *     id: int,
     *     image: string|null,
     *     title: string,
     *     slug: string,
     *     description: string,
     *     views: int,
     *     published_at: string
     * }>
     */
    public function findSimilar(
        int $postId,
        array $categoryIds,
        int $limit = 3,
    ): array {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = [];

        foreach ($categoryIds as $index => $categoryId) {
            $placeholders[] = ':category_' . $index;
        }

        $inClause = implode(', ', $placeholders);

        $statement = $this->pdo->prepare(
            "
            SELECT
                p.id,
                p.image,
                p.title,
                p.slug,
                p.description,
                p.views,
                p.published_at,
                COUNT(DISTINCT pc.category_id) AS matched_categories
            FROM posts p
            INNER JOIN post_categories pc ON pc.post_id = p.id
            WHERE pc.category_id IN ({$inClause})
                AND p.id != :post_id
            GROUP BY
                p.id,
                p.image,
                p.title,
                p.slug,
                p.description,
                p.views,
                p.published_at
            ORDER BY
                matched_categories DESC,
                p.published_at DESC
            LIMIT :limit
            ",
        );

        foreach ($categoryIds as $index => $categoryId) {
            $statement->bindValue(
                ':category_' . $index,
                $categoryId,
                PDO::PARAM_INT,
            );
        }

        $statement->bindValue(
            ':post_id',
            $postId,
            PDO::PARAM_INT,
        );

        $statement->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT,
        );

        $statement->execute();

        /** @var list<array{
         *     id: int,
         *     image: string|null,
         *     title: string,
         *     slug: string,
         *     description: string,
         *     views: int,
         *     published_at: string
         * }> $posts
         */
        $posts = $statement->fetchAll();

        return $posts;
    }

    /**
     * @param list<int> $categoryIds
     *
     * @return list<array{
     *     id: int,
     *     image: string|null,
     *     title: string,
     *     slug: string,
     *     description: string,
     *     views: int,
     *     published_at: string,
     *     category_id: int
     * }>
     */
    public function findLatestForCategories(
        array $categoryIds,
        int $limitPerCategory = 3,
    ): array {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = [];

        foreach ($categoryIds as $index => $categoryId) {
            $placeholders[] = ':category_' . $index;
        }

        $inClause = implode(', ', $placeholders);

        $sql = "
    SELECT
        id,
        image,
        title,
        slug,
        description,
        views,
        published_at,
        category_id
    FROM (
        SELECT
            p.id,
            p.image,
            p.title,
            p.slug,
            p.description,
            p.views,
            p.published_at,
            pc.category_id,
            ROW_NUMBER() OVER (
                PARTITION BY pc.category_id
                ORDER BY p.published_at DESC, p.id DESC
            ) AS rn
        FROM posts p
        INNER JOIN post_categories pc
            ON pc.post_id = p.id
        WHERE pc.category_id IN ({$inClause})
    ) ranked_posts
    WHERE rn <= :limit
    ORDER BY
        category_id,
        published_at DESC,
        id DESC
";

        $statement = $this->pdo->prepare($sql);

        foreach ($categoryIds as $index => $categoryId) {
            $statement->bindValue(
                ':category_' . $index,
                $categoryId,
                PDO::PARAM_INT,
            );
        }

        $statement->bindValue(
            ':limit',
            $limitPerCategory,
            PDO::PARAM_INT,
        );

        $statement->execute();

        /** @var list<array{
         *     id: int,
         *     image: string|null,
         *     title: string,
         *     slug: string,
         *     description: string,
         *     views: int,
         *     published_at: string,
         *     category_id: int
         * }> $posts
         */
        $posts = $statement->fetchAll();

        return $posts;
    }
}
