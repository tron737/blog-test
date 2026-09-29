<?php

namespace App\Controllers;

use App\Core\View;

abstract class AbstractController
{
    public function __construct(
        protected readonly View $view,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function render(string $template, array $data = []): void
    {
        $this->view->render($template, $data);
    }

    protected function notFound(): void
    {
        http_response_code(404);

        $this->render('404.tpl');
    }
}