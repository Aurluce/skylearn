<?php
declare(strict_types=1);

// Identifiants MeSomb : à vérifier avec la documentation officielle du fournisseur
return [
    'provider'        => 'mesomb',
    'application_key' => Env::get('MESOMB_APPLICATION_KEY', ''),
    'access_key'      => Env::get('MESOMB_ACCESS_KEY', ''),
    'secret_key'      => Env::get('MESOMB_SECRET_KEY', ''),
    'currency'        => 'XAF',
];
