<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    public const STATUSES = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];

    protected $fillable = [
        'reference',
        'service_id',
        'stylist_id',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'scheduled_at',
        'ends_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->reference)) {
                $booking->reference = 'SB-'.strtoupper(Str::random(8));
            }
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function stylist(): BelongsTo
    {
        return $this->belongsTo(Stylist::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'no_show' => 'Tidak Hadir',
            default => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'confirmed' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'no_show' => 'rose',
            default => 'gray',
        };
    }

    /**
     * Check if this booking can still be cancelled by the client.
     * Must be at least 2 hours before scheduled time.
     */
    public function canBeCancelledByClient(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->scheduled_at->diffInHours(now()) >= 2;
    }

    public function whatsappUrl(): string
    {
        $phone = $this->customer_phone;
        // Clean phone number: remove non-digits
        $phone = preg_replace('/\D/', '', $phone);
        
        // Convert leading 0 to 62 (Indonesian country code)
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        
        // Default to international format if length is reasonable but doesn't have 62
        if (!str_starts_with($phone, '62') && strlen($phone) >= 9) {
            $phone = '62' . $phone;
        }

        $dateFormatted = $this->scheduled_at->timezone(config('app.timezone'))->format('d M Y');
        $timeFormatted = $this->scheduled_at->timezone(config('app.timezone'))->format('H:i');
        $stylistName = $this->stylist->name ?? 'Stylist Pilihan';

        $message = "Halo {$this->customer_name}, kami dari *Glow Studio* ingin mengonfirmasi jadwal booking Anda untuk perawatan *{$this->service->name}* pada tanggal *{$dateFormatted}* pukul *{$timeFormatted} WIB* dengan stylist *{$stylistName}*.\n\nApakah jadwal ini sudah sesuai? Terima kasih! ✨";

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }
}
