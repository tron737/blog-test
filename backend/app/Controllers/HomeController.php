<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;

class HomeController extends AbstractController
{
    public function __construct(
        View $view,
        private readonly CategoryRepository $categoryRepository,
        private readonly PostRepository $postRepository,
    ) {
        parent::__construct($view);
    }

    public function index(): void
    {
        $categories = $this->categoryRepository->findAllWithPosts();

        $categoryIds = array_column(
            $categories,
            'id',
        );

        $posts = $this->postRepository->findLatestForCategories(
            $categoryIds,
            3,
        );

        $postsByCategory = [];

        foreach ($posts as $post) {
            $postsByCategory[$post['category_id']][] = $post;
        }

        foreach ($categories as &$category) {
            $category['posts'] = $postsByCategory[$category['id']] ?? [];
        }

        unset($category);

        $this->render('home.tpl', [
            'categories' => $categories,
        ]);
    }
}
