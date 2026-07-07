#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="/var/www/scout-hut-mgmt"
BRANCH="main"

if [[ $EUID -ne 0 ]]; then echo "Run this script with sudo or as root."; exit 1; fi
if [[ ! -d "$APP_DIR/.git" ]]; then echo "No Git checkout found at $APP_DIR"; exit 1; fi

cd "$APP_DIR"
set -a
# shellcheck disable=SC1091
source .env
set +a

BACKUP_DIR="$APP_DIR/storage/backups"
STAMP="$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"
chmod 750 "$BACKUP_DIR"

mysqldump --single-transaction --routines --triggers -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" | gzip > "$BACKUP_DIR/database-${STAMP}.sql.gz"
cp .env "$BACKUP_DIR/env-${STAMP}.backup"
chown root:www-data "$BACKUP_DIR/env-${STAMP}.backup"
chmod 640 "$BACKUP_DIR/env-${STAMP}.backup"

CURRENT="$(git rev-parse --short HEAD)"
git fetch origin "$BRANCH"
git reset --hard "origin/$BRANCH"
composer install --no-dev --prefer-dist --optimize-autoloader
mkdir -p storage/uploads storage/logs storage/backups public/assets/brand
chown -R www-data:www-data storage
find storage -type d -exec chmod 750 {} \;
find storage -type f -exec chmod 640 {} \;
php scripts/migrate.php

find "$BACKUP_DIR" -type f -mtime +30 -delete
PHP_SERVICE="$(systemctl list-unit-files --type=service | awk '/^php[0-9.]+-fpm\.service/ {print $1; exit}')"
if [[ -n "$PHP_SERVICE" ]]; then systemctl restart "$PHP_SERVICE"; fi
nginx -t && systemctl reload nginx

echo "Updated Scout Hut Management from ${CURRENT} to $(git rev-parse --short HEAD)."
echo "Backup created: $BACKUP_DIR/database-${STAMP}.sql.gz"
