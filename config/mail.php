<?php
declare(strict_types=1);

return [
    'host'       => Env::get('MAIL_HOST', 'smtp.gmail.com'),
    'port'       => (int) Env::get('MAIL_PORT', '587'),
    'username'   => Env::get('MAIL_USERNAME', 'aurlucefeudjio8@gmail.com'),
    'password'   => Env::get('MAIL_PASSWORD', 'rqzgcdopftoexhsj'),
    'encryption' => Env::get('MAIL_ENCRYPTION', 'tls'), // tls ou ssl
    'from_email' => Env::get('MAIL_FROM_EMAIL', 'no-reply@skylearn.cm'),
    'from_name'  => Env::get('MAIL_FROM_NAME', 'SKYLEARN'),
    'reply_to'   => Env::get('MAIL_REPLY_TO', 'contact@skylearn.cm'),
];