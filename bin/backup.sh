#!/usr/bin/env bash
# Sauvegarde de la base MySQL et des fichiers téléversés
set -euo pipefail
cd "$(dirname "$0")/.."
set -a; source .env; set +a
STAMP="$(date +%Y%m%d_%H%M%S)"
mkdir -p storage/backups
MYSQL_PWD="$DB_PASS" mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" \
  | gzip > "storage/backups/db_$STAMP.sql.gz"
tar -czf "storage/backups/files_$STAMP.tar.gz" storage/uploads
echo "Sauvegarde terminée : storage/backups/*_$STAMP.*"
