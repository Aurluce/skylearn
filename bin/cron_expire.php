<?php
// Marque comme expirés les abonnements dont la date de fin est dépassée.
// À planifier avec cron, en complément de la vérification faite à chaque accès.
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/core/autoload.php';
Env::load(BASE_PATH . '/.env');

$n = Database::pdo()->exec(
    "UPDATE subscriptions SET status = 'expired' WHERE status = 'active' AND ends_at <= NOW()"
);
echo "Abonnements expirés : $n\n";
