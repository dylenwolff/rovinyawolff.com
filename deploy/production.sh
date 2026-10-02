#!/usr/bin/env bash
set -euo pipefail

app_dir=/home/rovinyawolff/web/rovinyawolff.com/public_html/app
deploy_key=/home/rovinyawolff/.ssh/rovinyawolff_github

cd "$app_dir"

exec 9>/tmp/rovinyawolff-production-deploy.lock
if ! flock -n 9; then
    echo "Another deployment is already running." >&2
    exit 1
fi

cleanup() {
    php artisan up >/dev/null 2>&1 || true
}
trap cleanup EXIT

export GIT_SSH_COMMAND="ssh -i $deploy_key -o IdentitiesOnly=yes"

git -c safe.directory="$app_dir" pull --ff-only origin main
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan livewire:publish --assets
npm ci
npm run build

php artisan down --retry=60
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan up

trap - EXIT
echo "Production deployment complete: $(git -c safe.directory="$app_dir" rev-parse --short HEAD)"
