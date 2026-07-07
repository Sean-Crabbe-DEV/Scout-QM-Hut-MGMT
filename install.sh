#!/usr/bin/env bash
set -Eeuo pipefail

REPO_URL="https://github.com/Sean-Crabbe-DEV/Scout-QM-Hut-MGMT.git"
APP_DIR="/var/www/scout-hut-mgmt"
DOMAIN=""
DB_NAME="scout_hut_mgmt"
DB_USER="scout_hut_user"
DB_PASS=""
WITH_CERTBOT=0
APP_URL=""

usage() {
  cat <<USAGE
Usage: sudo bash install.sh [options]
  --domain DOMAIN          Public hostname, e.g. hut.example.org (required for a proper Nginx site)
  --app-dir PATH           Install directory (default: /var/www/scout-hut-mgmt)
  --db-name NAME           MariaDB database name
  --db-user NAME           MariaDB database user
  --db-password PASSWORD   Database password (generated if omitted)
  --app-url URL            Public base URL; defaults to http://DOMAIN (use https:// when behind a TLS proxy)
  --with-certbot           Install Certbot and request a certificate after Nginx is configured
                           (do not use when Cloudflare Tunnel terminates HTTPS)
  --help                   Show this help
USAGE
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --domain) DOMAIN="$2"; shift 2;;
    --app-dir) APP_DIR="$2"; shift 2;;
    --db-name) DB_NAME="$2"; shift 2;;
    --db-user) DB_USER="$2"; shift 2;;
    --db-password) DB_PASS="$2"; shift 2;;
    --app-url) APP_URL="$2"; shift 2;;
    --with-certbot) WITH_CERTBOT=1; shift;;
    --help) usage; exit 0;;
    *) echo "Unknown option: $1"; usage; exit 1;;
  esac
done

if [[ $EUID -ne 0 ]]; then echo "Run this script with sudo or as root."; exit 1; fi
if [[ -z "$DOMAIN" ]]; then echo "--domain is required, for example: --domain hut.example.org"; exit 1; fi
if [[ ! "$DOMAIN" =~ ^[A-Za-z0-9.-]+$ ]]; then echo "Invalid domain name."; exit 1; fi
# Re-running the installer must never desynchronise the existing .env and MariaDB password.
if [[ -z "$DB_PASS" && -f "$APP_DIR/.env" ]]; then
  DB_PASS="$(sed -n 's/^DB_PASSWORD=//p' "$APP_DIR/.env" | head -n1)"
fi
if [[ -z "$DB_PASS" ]]; then DB_PASS="$(openssl rand -base64 28 | tr -dc 'A-Za-z0-9' | head -c 24)"; fi
if ! [[ "$DB_PASS" =~ ^[A-Za-z0-9]{12,128}$ ]]; then echo "Database password must be 12–128 letters and numbers only."; exit 1; fi
if [[ -z "$APP_URL" ]]; then APP_URL="http://${DOMAIN}"; fi
if [[ "$WITH_CERTBOT" -eq 1 && "$APP_URL" == "http://${DOMAIN}" ]]; then APP_URL="https://${DOMAIN}"; fi

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y git curl ca-certificates nginx mariadb-server composer php-cli php-fpm php-mysql php-mbstring php-xml php-curl php-zip php-gd unzip

if [[ ! -d "$APP_DIR/.git" ]]; then
  mkdir -p "$(dirname "$APP_DIR")"
  git clone "$REPO_URL" "$APP_DIR"
else
  echo "Existing Git checkout found at $APP_DIR; use update.sh for upgrades."
fi

cd "$APP_DIR"
if [[ ! -f .env ]]; then
  APP_KEY="$(openssl rand -hex 32)"
  cat > .env <<ENV
APP_ENV=production
APP_URL=${APP_URL}
APP_DOMAIN=${DOMAIN}
APP_KEY=${APP_KEY}
APP_NAME="1st Sedbury & Tidenham Scouts"
APP_TIMEZONE=Europe/London

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}

MAIL_FROM_ADDRESS=hut-management@${DOMAIN}
MAIL_FROM_NAME="1st Sedbury & Tidenham Scouts"
UPLOAD_MAX_MB=8
ENV
  chown root:www-data .env
  chmod 640 .env
else
  echo ".env already exists; keeping existing configuration."
fi

mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

composer install --no-dev --prefer-dist --optimize-autoloader
mkdir -p storage/uploads storage/logs storage/backups public/assets/brand
chown -R www-data:www-data storage
find storage -type d -exec chmod 750 {} \;
find storage -type f -exec chmod 640 {} \;
php scripts/migrate.php

PHP_SERVICE="$(systemctl list-unit-files --type=service | awk '/^php[0-9.]+-fpm\.service/ {print $1; exit}')"
if [[ -n "$PHP_SERVICE" ]]; then systemctl enable --now "$PHP_SERVICE"; fi
PHP_SOCKET="$(find /run/php -maxdepth 1 -type s -name 'php*-fpm.sock' | head -n1 || true)"
if [[ -z "$PHP_SOCKET" ]]; then
  echo "Could not find PHP-FPM socket in /run/php. Check php-fpm installation."; exit 1
fi

SITE_FILE="/etc/nginx/sites-available/scout-hut-mgmt"
cat > "$SITE_FILE" <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};
    root ${APP_DIR}/public;
    index index.php;
    client_max_body_size 10m;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:${PHP_SOCKET};
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. { deny all; }
    location ~* /(app|database|scripts|storage)/ { deny all; }
}
NGINX
ln -sfn "$SITE_FILE" /etc/nginx/sites-enabled/scout-hut-mgmt
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl enable --now nginx mariadb
systemctl reload nginx

cat > /etc/cron.d/scout-hut-mgmt <<CRON
# Daily inspection and overdue-ticket reminder task
15 7 * * * www-data /usr/bin/php ${APP_DIR}/cron.php >> ${APP_DIR}/storage/logs/cron.log 2>&1
CRON
chmod 644 /etc/cron.d/scout-hut-mgmt

if [[ "$WITH_CERTBOT" -eq 1 ]]; then
  apt-get install -y certbot python3-certbot-nginx
  if ! certbot --nginx -d "$DOMAIN"; then
    echo "Certificate request did not complete. The application is installed; check DNS/origin reachability before retrying Certbot."
  fi
fi

echo
echo "Installation complete."
echo "Open: ${APP_URL}"
echo "The first person to visit the site must use 'Set up the first Admin account'."
echo "Database credentials are saved in: ${APP_DIR}/.env"
echo "Before real use: configure SMTP under Admin > System settings and add the approved red Group logo. When using Cloudflare Tunnel, do not pass --with-certbot."
