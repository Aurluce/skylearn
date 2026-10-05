<?php
declare(strict_types=1);

return [
    'name'     => Env::get('APP_NAME', 'SKYLEARN'),
    'env'      => Env::get('APP_ENV', 'local'),
    'debug'    => Env::get('APP_DEBUG', 'false') === 'true',
    'url'      => Env::get('APP_URL', 'http://localhost:8000'),
    'whatsapp' => Env::get('WHATSAPP_NUMBER', ''),
];
