<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/core/autoload.php';
require BASE_PATH . '/core/helpers.php';

Env::load(BASE_PATH . '/.env');
$config = require BASE_PATH . '/config/app.php';

ini_set('display_errors', $config['debug'] ? '1' : '0');
error_reporting(E_ALL);
date_default_timezone_set('Africa/Douala');

Session::start();

try {
    $router = new Router();
    require BASE_PATH . '/routes/web.php';
    require BASE_PATH . '/routes/api.php';
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    $logMessage = '[' . date('c') . '] ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    $logFile = BASE_PATH . '/storage/logs/app.log';
    $logDirectory = dirname($logFile);

    if (is_dir($logDirectory) && is_writable($logDirectory)
        && (!file_exists($logFile) || is_writable($logFile))) {
        error_log($logMessage, 3, $logFile);
    } else {
        // Fall back to the server's configured PHP error log instead of raising another warning.
        error_log($logMessage);
    }
    http_response_code(500);
    if ($config['debug']) {
        echo '<pre>' . e($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        require BASE_PATH . '/view/errors/500.php';
    }
}
