<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'phone',
        'name',
        'email',
        'birthday',
        'first_booking_at',
        'last_booking_at',
        'total_bookings',
        'total_spent',
        'preferred_stylist_id',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date',
            'first_booking_at' => 'datetime',
            'last_booking_at' => 'datetime',
            'total_bookings' => 'integer',
            'total_spent' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function preferredStylist(): BelongsTo
    {
        return $this->belongsTo(Stylist::class, 'preferred_stylist_id');
    }

    /**
     * Find or create a customer record from booking data.
     */
    public static function findOrCreateFromBooking(string $phone, string $name, ?string $email = null): self
    {
        $cleanPhone = preg_replace('/\D/', '', $phone);

        $customer = self::where('phone', $cleanPhone)->first();

        if ($customer) {
            $customer->update([
                'name' => $name,
                'email' => $email ?? $customer->email,
                'last_booking_at' => now(),
            ]);
            $customer->increment('total_bookings');
            return $customer;
        }

        return self::create([
            'phone' => $cleanPhone,
            'name' => $name,
            'email' => $email,
            'first_booking_at' => now(),
            'last_booking_at' => now(),
            'total_bookings' => 1,
        ]);
    }

    /**
     * Get formatted total spending.
     */
    public function formattedTotalSpent(): string
    {
        return 'Rp ' . number_format($this->total_spent, 0, ',', '.');
    }

    /**
     * Check if customer is considered "loyal" (5+ visits).
     */
    public function isLoyal(): bool
    {
        return $this->total_bookings >= 5;
    }

    /**
     * Check if customer is "at risk" of churning (30+ days since last visit).
     */
    public function isAtRisk(): bool
    {
        if (!$this->last_booking_at) {
            return false;
        }

        return $this->last_booking_at->diffInDays(now()) >= 30;
    }

    /**
     * Get the most frequently booked service.
     */
    public function favoriteService(): ?Service
    {
        $serviceId = $this->bookings()
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('service_id, COUNT(*) as count')
            ->groupBy('service_id')
            ->orderByDesc('count')
            ->value('service_id');

        return $serviceId ? Service::find($serviceId) : null;
    }
}
