<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\PostRepository;

class PostController extends AbstractController
{
    public function __construct(
        View $view,
        private readonly PostRepository $postRepository,
    ) {
        parent::__construct($view);
    }

    public function show(string $slug): void
    {
        $post = $this->postRepository->findBySlug($slug);

        if ($post === null) {
            $this->notFound();

            return;
        }

        $this->postRepository->incrementViews(
            $post['id'],
        );

        ++$post['views'];

        $categoryIds = $this->postRepository->findCategoryIdsByPost(
            $post['id'],
        );

        $similarPosts = $this->postRepository->findSimilar(
            $post['id'],
            $categoryIds,
            3,
        );

        $this->render('post.tpl', [
            'title' => $post['title'],
            'post' => $post,
            'similarPosts' => $similarPosts,
        ]);
    }
}
