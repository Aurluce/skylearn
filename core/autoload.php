<?php
declare(strict_types=1);

require_once BASE_PATH . '/vendor/autoload.php';

spl_autoload_register(static function (string $class): void {
    foreach (['core', 'controller', 'controller/admin', 'model'] as $dir) {
        $file = BASE_PATH . "/$dir/$class.php";
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});
