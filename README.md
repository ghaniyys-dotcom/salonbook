# SalonBook — Appointment & Admin Dashboard

Sistem booking salon untuk portofolio full-stack Laravel. Brand demo: **Glow Studio** (Jakarta).

## Fitur

- Landing publik + form booking (guest)
- Validasi jadwal bentrok per stylist
- Panel admin: dashboard, kelola layanan & stylist, pipeline status booking
- Role: `admin`, `staff`, `customer`
- Email konfirmasi (queue-ready)
- Export laporan CSV
- REST API v1 (`/api/v1/services`, `/api/v1/bookings`)

## Stack

- Laravel 12 + Breeze (Blade)
- SQLite (default) / MySQL
- Tailwind CSS + Vite
- Mail queue

## Demo akun

| Role  | Email           | Password  |
|-------|-----------------|-----------|
| Admin | admin@demo.com  | password  |
| Staff | staff@demo.com  | password  |

## Setup lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```

Buka http://127.0.0.1:8000 — Admin: http://127.0.0.1:8000/admin (login dulu).

## Deploy

Lihat [DEPLOY.md](DEPLOY.md) untuk Railway, Render, atau shared hosting.

## API

Lihat [docs/API.md](docs/API.md) dan `docs/salonbook-api.postman_collection.json`.

## Portofolio

Case study: `../portfolio-content/projects/salonbook.md`
