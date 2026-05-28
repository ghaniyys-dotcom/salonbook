# SalonBook API v1

Base URL: `{APP_URL}/api/v1`

## Services

### GET /services

Daftar layanan aktif beserta stylist.

```json
{
  "data": [
    {
      "id": 1,
      "name": "Haircut Premium",
      "slug": "haircut-premium",
      "price": 150000,
      "price_formatted": "Rp 150.000",
      "stylists": [{ "id": 1, "name": "Dewi Kartika" }]
    }
  ]
}
```

### GET /services/{id}

Detail satu layanan.

## Bookings

### GET /bookings?status=pending&per_page=15

Paginated list (untuk integrasi admin/mobile).

### POST /bookings

```json
{
  "service_id": 1,
  "stylist_id": 1,
  "customer_name": "Budi",
  "customer_email": "budi@example.com",
  "customer_phone": "081234567890",
  "scheduled_at": "2026-05-25 14:00:00",
  "notes": "Potong pendek"
}
```

Response `201` dengan object booking + relations.

### GET /bookings/{id}

Detail booking by ID.

## Errors

Validation errors return `422` dengan `message` dan `errors` object standar Laravel.
