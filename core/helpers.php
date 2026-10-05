<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_base_path(): string
{
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $appRoot = realpath(BASE_PATH);

    if ($documentRoot === false || $appRoot === false) {
        return '';
    }

    $documentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');
    $appRoot = str_replace('\\', '/', $appRoot);

    if ($appRoot === $documentRoot || !str_starts_with($appRoot, $documentRoot . '/')) {
        return '';
    }

    return '/' . trim(substr($appRoot, strlen($documentRoot)), '/');
}

function url(string $path = ''): string
{
    return app_base_path() . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function csrf_token(): string
{
    return Csrf::token();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function setting(string $key, ?string $default = null): ?string
{
    static $cache = null;

    if ($cache === null) {
        $cache = [];
        try {
            $rows = Database::pdo()
                ->query("SELECT setting_key, setting_value FROM settings")
                ->fetchAll();
            foreach ($rows as $row) {
                $cache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable $e) {
            // BD indisponible : on retournera les valeurs par défaut
            $cache = [];
        }
    }

    $value = $cache[$key] ?? null;

    return ($value === null || $value === '') ? $default : $value;
}

/**
 * Construit une URL WhatsApp propre : https://wa.me/237600000000?text=...
 */
function whatsapp_link(string $message = ''): string
{
    $raw = (string) setting('whatsapp_number', '');
    $num = preg_replace('/\D/', '', $raw);

    if ($num === '') {
        return '#';
    }

    $url = 'https://wa.me/' . $num;

    if ($message !== '') {
        $url .= '?text=' . rawurlencode($message);
    }

    return $url;
}

/**
 * Vérifie qu'une URL de réseau social est bien renseignée.
 */
function social_link(string $key): ?string
{
    $value = setting($key, '');
    return ($value && $value !== '#') ? $value : null;
}