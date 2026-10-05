#!/usr/bin/env bash
cd "$(dirname "$0")/.."
echo "SKYLEARN : http://localhost:8000  (Ctrl+C pour arrêter)"
php -S localhost:8000 -t public public/router.php
