#!/usr/bin/env bash
# Deploy a downloaded release over an existing Ubuntu CT installation.
# Keeps .env, storage/uploads, storage/backups and the existing Git metadata intact.
# This recovery deploy deliberately does NOT run Composer.
set -Eeuo pipefail

APP_DIR="/var/www/scout-hut-mgmt"
SOURCE_DIR="${1:-}"
RELEASE="v1.14"

if [[ $EUID -ne 0 ]]; then
  echo "Run with sudo or as root."
  exit 1
fi
if [[ -z "$SOURCE_DIR" || ! -f "$SOURCE_DIR/public/index.php" ]]; then
  echo "Usage: sudo bash deploy-update.sh /path/to/extracted/scout-hut-mgmt-v1.14"
  exit 1
fi
if [[ ! -f "$APP_DIR/.env" ]]; then
  echo "Existing installation not found at $APP_DIR (.env is missing)."
  exit 1
fi
if ! command -v rsync >/dev/null 2>&1; then
  echo "rsync is required for a safe update. Install it with: apt-get update && apt-get install -y rsync"
  exit 1
fi

set -a
# shellcheck disable=SC1090
source "$APP_DIR/.env"
set +a

STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$APP_DIR/storage/backups"
mkdir -p "$BACKUP_DIR"
chmod 750 "$BACKUP_DIR"

if command -v mysqldump >/dev/null 2>&1; then
  mysqldump --single-transaction --routines --triggers \
    -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" \
    -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" \
    | gzip > "$BACKUP_DIR/database-before-${RELEASE}-${STAMP}.sql.gz"
fi
cp "$APP_DIR/.env" "$BACKUP_DIR/env-before-${RELEASE}-${STAMP}.backup"
chown root:www-data "$BACKUP_DIR/env-before-${RELEASE}-${STAMP}.backup"
chmod 640 "$BACKUP_DIR/env-before-${RELEASE}-${STAMP}.backup"

# Preserve live data, credentials and the original Git checkout. Application files are replaced.
rsync -a --delete \
  --exclude='.env' \
  --exclude='storage/' \
  --exclude='.git/' \
  "$SOURCE_DIR/" "$APP_DIR/"

# The previous GitHub copy has a broken composer.json. This release includes a valid one,
# but Composer is intentionally skipped so deployment is not blocked by Composer or GitHub.
printf '%s\n' 'Composer was deliberately skipped for this release.'

mkdir -p "$APP_DIR/storage/uploads" "$APP_DIR/storage/logs" "$APP_DIR/storage/backups" "$APP_DIR/public/assets/brand"
php "$APP_DIR/scripts/migrate.php"

chown -R www-data:www-data "$APP_DIR/storage"
find "$APP_DIR/storage" -type d -exec chmod 750 {} \;
find "$APP_DIR/storage" -type f -exec chmod 640 {} \;
chown root:www-data "$APP_DIR/.env"
chmod 640 "$APP_DIR/.env"

PHP_SERVICE="$(systemctl list-unit-files --type=service | awk '/^php[0-9.]+-fpm\.service/ {print $1; exit}')"
if [[ -n "$PHP_SERVICE" ]]; then systemctl restart "$PHP_SERVICE"; fi
nginx -t && systemctl reload nginx

echo "Scout Hut Management ${RELEASE} deployed successfully."
echo "Backup: $BACKUP_DIR/database-before-${RELEASE}-${STAMP}.sql.gz"
echo "Use the downloaded release deploy script for updates until the GitHub repository is repaired."
