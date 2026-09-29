<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

class CategoryController extends AbstractController
{
    private const POSTS_PER_PAGE = 6;

    private const ALLOWED_SORTS = [
        'date',
        'views',
    ];

    public function __construct(
        View $view,
        private readonly CategoryRepository $categoryRepository,
        private readonly PostRepository $postRepository,
    ) {
        parent::__construct($view);
    }

    public function show(string $slug): void
    {
        $category = $this->categoryRepository->findBySlug($slug);

        if ($category === null) {
            $this->notFound();

            return;
        }

        $sort = $this->resolveSort(
            $_GET['sort'] ?? null,
        );

        $page = $this->resolvePage(
            $_GET['page'] ?? null,
        );

        $totalPosts = $this->postRepository->countByCategory(
            $category['id'],
        );

        $totalPages = max(
            1,
            (int) ceil($totalPosts / self::POSTS_PER_PAGE),
        );

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * self::POSTS_PER_PAGE;

        $posts = $this->postRepository->findByCategory(
            $category['id'],
            $sort,
            self::POSTS_PER_PAGE,
            $offset,
        );

        $this->render('category.tpl', [
            'title' => $category['name'],
            'category' => $category,
            'posts' => $posts,
            'sort' => $sort,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    private function resolveSort(mixed $sort): string
    {
        if (!is_string($sort)) {
            return 'date';
        }

        if (!in_array($sort, self::ALLOWED_SORTS, true)) {
            return 'date';
        }

        return $sort;
    }

    private function resolvePage(mixed $page): int
    {
        if (!is_string($page) && !is_int($page)) {
            return 1;
        }

        $page = filter_var(
            $page,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ],
        );

        if ($page === false) {
            return 1;
        }

        return $page;
    }
}
