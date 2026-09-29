<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class CategoryController
{
    public function __construct(
        private readonly View $view,
    ) {
    }

    public function show(string $slug): void
    {
        $this->view->render('category.tpl', [
            'slug' => $slug,
        ]);
    }
}
