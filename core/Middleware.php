<?php
declare(strict_types=1);

final class Middleware
{
    public static function run(string $name): void
    {
        $path  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $isApi = str_starts_with($path, '/api/');

        switch ($name) {
            case 'auth':
                if (!Auth::check()) {
                    if ($isApi) {
                        Response::json(['error' => 'Non authentifié'], 401);
                    }
                    Response::redirect('/login');
                }
                break;

            case 'guest':
                if (Auth::check()) {
                    Response::redirect('/dashboard');
                }
                break;

            case 'admin':
                if (!Auth::isAdmin()) {
                    if ($isApi) {
                        Response::json(['error' => 'Accès refusé'], 403);
                    }
                    http_response_code(403);
                    require BASE_PATH . '/view/errors/403.php';
                    exit;
                }
                break;
        }
    }
}
