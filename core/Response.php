<?php
declare(strict_types=1);

final class Response
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function redirect(string $path): void
    {
        if (str_starts_with($path, '/') && function_exists('app_base_path')) {
            $basePath = app_base_path();
            if ($basePath !== '' && $path !== $basePath && !str_starts_with($path, $basePath . '/')) {
                $path = $basePath . ($path === '/' ? '/' : $path);
            }
        }

        header('Location: ' . $path);
        exit;
    }
}
