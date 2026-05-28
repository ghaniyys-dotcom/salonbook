<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingLog extends Model
{
    protected $fillable = [
        'booking_id',
        'action',
        'description',
        'user_id',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an action for a booking.
     */
    public static function log(int $bookingId, string $action, ?string $description = null): self
    {
        return static::create([
            'booking_id' => $bookingId,
            'action' => $action,
            'description' => $description,
            'user_id' => auth()->id(),
        ]);
    }
}
