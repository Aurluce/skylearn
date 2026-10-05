<?php
declare(strict_types=1);

abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require BASE_PATH . "/view/$view.php";
        $content = ob_get_clean();
        require BASE_PATH . "/view/layouts/$layout.php";
    }

    protected function json(array $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    /** Lit le corps JSON (Fetch) ou, à défaut, $_POST */
    protected function input(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw !== false && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return $_POST;
    }
}
