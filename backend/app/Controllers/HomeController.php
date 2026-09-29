<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

class HomeController
{
    public function __construct(
        private readonly View $view,
    ) {
    }

    public function index(): void
    {
        $this->view->render('home.tpl', [
            'title' => 'Мой блог',
        ]);
    }
}
