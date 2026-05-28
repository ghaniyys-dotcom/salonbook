<x-mail::message>
# Booking Diterima

Halo **{{ $booking->customer_name }}**,

Terima kasih telah booking di **Glow Studio**. Berikut detailnya:

**Referensi:** {{ $booking->reference }}  
**Layanan:** {{ $booking->service->name }}  
**Stylist:** {{ $booking->stylist->name }}  
**Jadwal:** {{ $booking->scheduled_at->timezone(config('app.timezone'))->format('d M Y, H:i') }} WIB  
**Status:** {{ $booking->statusLabel() }}

Tim kami akan mengonfirmasi booking Anda segera.

<x-mail::button :url="config('app.url')">
Kunjungi Website
</x-mail::button>

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
