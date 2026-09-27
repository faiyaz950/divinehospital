#!/bin/bash
#
# Deploys the site on cPanel shared hosting (BigRock). Run by cPanel's
# "Git Version Control → Deploy HEAD Commit" through .cpanel.yml, or by hand:
#   bash deploy.sh
#
# The domain's document root must point to the "public" folder of this repository.
# Only directory permissions are changed: changing tracked file modes would leave
# the working tree dirty and cPanel refuses to deploy a dirty repository.

set -euo pipefail

cd "$(dirname "$0")"

PHP=/opt/cpanel/ea-php84/root/usr/bin/php
if [ ! -x "$PHP" ]; then
    PHP=/opt/cpanel/ea-php83/root/usr/bin/php
fi
if [ ! -x "$PHP" ]; then
    PHP=php
fi

echo "Using $("$PHP" -r 'echo PHP_BINARY, " (PHP ", PHP_VERSION, ")";')"

if [ ! -f composer.phar ]; then
    echo "Downloading Composer..."
    "$PHP" -r "copy('https://getcomposer.org/download/latest-stable/composer.phar', 'composer.phar');"
fi

if [ ! -f .env ]; then
    echo "Creating .env for production..."
    cp .env.example .env
    sed -i.bak \
        -e 's/^APP_ENV=.*/APP_ENV=production/' \
        -e 's/^APP_DEBUG=.*/APP_DEBUG=false/' \
        -e 's|^APP_URL=.*|APP_URL=https://hospitaldivine.in|' \
        .env
    rm -f .env.bak
fi

"$PHP" composer.phar install --no-dev --optimize-autoloader --no-interaction --no-progress

if ! grep -q '^APP_KEY=base64:' .env; then
    "$PHP" artisan key:generate --force --no-interaction
fi

touch database/database.sqlite
mkdir -p public/images/uploads
find storage bootstrap/cache database public/images/uploads -type d -exec chmod 775 {} +

"$PHP" artisan migrate --force --no-interaction
"$PHP" artisan optimize:clear
"$PHP" artisan optimize

echo "Deployed. If this is the first deploy, create the admin login with:"
echo "  $PHP artisan admin:create"
