<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    /**
     * @var list<array{
     *     method: string,
     *     path: string,
     *     handler: callable
     * }>
     */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = $this->normalizePath($path);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->createPattern($route['path']);

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            $params = array_filter(
                $matches,
                static fn ($key) => is_string($key),
                ARRAY_FILTER_USE_KEY,
            );

            call_user_func_array(
                $route['handler'],
                array_values($params),
            );

            return;
        }

        $this->notFound();
    }

    private function createPattern(string $path): string
    {
        $pattern = preg_replace(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            '(?P<$1>[^/]+)',
            $path,
        );

        return '#^' . $pattern . '$#';
    }

    private function normalizePath(string $path): string
    {
        if ($path === '/') {
            return '/';
        }

        return '/' . trim($path, '/');
    }

    private function notFound(): void
    {
        http_response_code(404);

        echo '404 Not Found';
    }
}
