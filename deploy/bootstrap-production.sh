#!/usr/bin/env bash
set -euo pipefail

web_root=/home/rovinyawolff/web/rovinyawolff.com/public_html
app_dir="$web_root/app"
deploy_key=/home/rovinyawolff/.ssh/rovinyawolff_github

cd "$app_dir"

if ! /usr/local/hestia/bin/v-list-databases rovinyawolff plain | awk '{print $1}' | grep -qx rovinyawolff_portfolio; then
    db_password=$(openssl rand -base64 30 | tr -dc A-Za-z0-9 | head -c 28)
    /usr/local/hestia/bin/v-add-database rovinyawolff portfolio portfolio "$db_password" mysql localhost utf8mb4
else
    db_password=$(/usr/local/hestia/bin/v-list-database rovinyawolff portfolio json | php -r '$data=json_decode(stream_get_contents(STDIN), true); $record=reset($data); echo $record["PASSWORD"] ?? "";')
fi

if [[ ! -f .env ]]; then
    cp .env.example .env
    sed -i \
        -e 's|^APP_ENV=.*|APP_ENV=production|' \
        -e 's|^APP_DEBUG=.*|APP_DEBUG=false|' \
        -e 's|^APP_URL=.*|APP_URL=https://rovinyawolff.com|' \
        -e 's|^ADMIN_EMAIL=.*|ADMIN_EMAIL=rovinyask@gmail.com|' \
        -e 's|^DB_CONNECTION=.*|DB_CONNECTION=mysql|' \
        -e 's|^DB_HOST=.*|DB_HOST=127.0.0.1|' \
        -e 's|^DB_PORT=.*|DB_PORT=3306|' \
        -e 's|^DB_DATABASE=.*|DB_DATABASE=rovinyawolff_portfolio|' \
        -e 's|^DB_USERNAME=.*|DB_USERNAME=rovinyawolff_portfolio|' \
        -e "s|^DB_PASSWORD=.*|DB_PASSWORD=$db_password|" \
        .env
fi

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link || true
php artisan optimize

chown -R rovinyawolff:www-data "$app_dir"
chmod -R ug+rwX storage bootstrap/cache

for config in \
    /home/rovinyawolff/conf/web/rovinyawolff.com/nginx.conf \
    /home/rovinyawolff/conf/web/rovinyawolff.com/nginx.ssl.conf
do
    sed -i "s|root        $web_root;|root        $app_dir/public;|" "$config"
    if ! grep -q 'try_files \$uri \$uri/ /index.php?\$query_string;' "$config"; then
        sed -i '/^[[:space:]]*location \/ {/a\		try_files $uri $uri/ /index.php?$query_string;' "$config"
    fi
done

nginx -t
systemctl reload nginx
echo "Production deployment complete: $(git rev-parse --short HEAD)"
