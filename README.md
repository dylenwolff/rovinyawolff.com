# Rovinya Wolff Portfolio

An editorial portfolio and lightweight CMS for Rovinya Wolff, built with Laravel 13, Filament 5, Tailwind CSS, Vite, PDF.js, and StPageFlip.

## Local setup

1. Copy `.env.example` to `.env` and configure the database, mail, admin domain, and admin email.
2. Run `composer install` and `npm install`.
3. Run `php artisan key:generate`, `php artisan migrate`, and `php artisan storage:link`.
4. Create the administrator with `php artisan make:filament-user` using the email configured in `ADMIN_EMAIL`.
5. Run `npm run build` for production assets.

Publications can be uploaded as PDFs in the Projects area. Posters, campaigns, and social designs use the cover and gallery fields. PDF downloads remain disabled unless enabled per project.
