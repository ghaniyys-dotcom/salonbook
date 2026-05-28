# Deploy SalonBook

## Railway / Render

1. Push repo ke GitHub.
2. New Web Service → connect repo.
3. Build: `composer install --no-dev --optimize-autoloader && npm ci && npm run build`
4. Start: `php artisan migrate --force && php artisan db:seed --class=SalonBookSeeder --force && php artisan serve --host=0.0.0.0 --port=$PORT`
5. Env vars:
   - `APP_KEY` (generate: `php artisan key:generate --show`)
   - `APP_URL` = URL production
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `DB_CONNECTION=sqlite` atau MySQL credentials
   - `QUEUE_CONNECTION=database` (opsional untuk email)

## Shared hosting

1. Upload ke `public_html` — arahkan document root ke folder `public/`.
2. Import database atau gunakan SQLite di `database/database.sqlite`.
3. `php artisan migrate --force && php artisan db:seed --class=SalonBookSeeder`

## Setelah deploy

- Ganti password demo admin di production.
- Set `MAIL_*` untuk email live (Mailtrap untuk staging).
