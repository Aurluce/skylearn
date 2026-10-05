<?php
declare(strict_types=1);

final class Router
{
    private array $routes = [];

    /** Chemins exemptés de la vérification CSRF (notifications serveur du fournisseur de paiement) */
    private array $csrfExempt = ['/api/payments/webhook'];

    public function get(string $path, string $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, string $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    private function add(string $method, string $path, string $handler, array $middleware): void
    {
        $this->routes[] = compact('method', 'path', 'handler', 'middleware');
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = '/' . trim(parse_url($uri, PHP_URL_PATH) ?: '/', '/');
        $basePath = app_base_path();

        if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        // Support both /app/... and /app/public/... when the project is under htdocs.
        if ($path === '/public') {
            $path = '/';
        } elseif (str_starts_with($path, '/public/')) {
            $path = substr($path, strlen('/public'));
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $route['path']) . '$#';
            if (!preg_match($regex, $path, $matches)) {
                continue;
            }

            if ($method === 'POST'
                && !in_array($path, $this->csrfExempt, true)
                && !Csrf::verify()) {
                http_response_code(419);
                echo 'Session expirée, rechargez la page.';
                return;
            }

            foreach ($route['middleware'] as $name) {
                Middleware::run($name);
            }

            $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
            [$class, $action] = explode('@', $route['handler']);
            (new $class())->$action(...$params);
            return;
        }

        http_response_code(404);
        require BASE_PATH . '/view/errors/404.php';
    }
}
