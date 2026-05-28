<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingAvailabilityService
{
    /**
     * Buffer time (minutes) between bookings for stylist rest/cleanup.
     */
    public const BUFFER_MINUTES = 15;

    public function assertSlotAvailable(
        int $stylistId,
        int $serviceId,
        Carbon $scheduledAt,
        ?int $excludeBookingId = null
    ): void {
        // Verify service is active
        $service = Service::findOrFail($serviceId);
        if (!$service->is_active) {
            throw ValidationException::withMessages([
                'service_id' => 'Layanan ini sedang tidak tersedia.',
            ]);
        }

        // Verify stylist is active
        $stylist = Stylist::findOrFail($stylistId);
        if (!$stylist->is_active) {
            throw ValidationException::withMessages([
                'stylist_id' => 'Stylist ini sedang tidak tersedia.',
            ]);
        }

        $endsAt = $scheduledAt->copy()->addMinutes($service->duration_minutes);

        // Use DB transaction with lockForUpdate to prevent race conditions (double booking)
        $conflict = DB::transaction(function () use ($stylistId, $scheduledAt, $endsAt, $excludeBookingId) {
            return Booking::query()
                ->where('stylist_id', $stylistId)
                ->whereNotIn('status', ['cancelled'])
                ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
                ->where(function ($query) use ($scheduledAt, $endsAt) {
                    $query->whereBetween('scheduled_at', [$scheduledAt, $endsAt->copy()->subMinute()])
                        ->orWhereBetween('ends_at', [$scheduledAt->copy()->addMinute(), $endsAt])
                        ->orWhere(function ($q) use ($scheduledAt, $endsAt) {
                            $q->where('scheduled_at', '<=', $scheduledAt)
                                ->where('ends_at', '>=', $endsAt);
                        });
                })
                ->lockForUpdate()
                ->exists();
        });

        if ($conflict) {
            throw ValidationException::withMessages([
                'scheduled_at' => 'Jadwal stylist sudah terisi. Pilih waktu lain.',
            ]);
        }
    }

    public function calculateEndsAt(Service $service, Carbon $scheduledAt): Carbon
    {
        return $scheduledAt->copy()->addMinutes($service->duration_minutes + self::BUFFER_MINUTES);
    }

    public function getUnavailableSlots(int $stylistId, int $serviceId, Carbon $date): array
    {
        $service = Service::findOrFail($serviceId);
        $slots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
        $unavailableSlots = [];

        // Fetch all active (non-cancelled) bookings for this stylist on the selected date
        $bookings = Booking::query()
            ->where('stylist_id', $stylistId)
            ->whereNotIn('status', ['cancelled'])
            ->whereDate('scheduled_at', $date->toDateString())
            ->get();

        foreach ($slots as $slot) {
            $slotStart = Carbon::parse($date->toDateString() . ' ' . $slot)->timezone(config('app.timezone'));
            $slotEnd = $slotStart->copy()->addMinutes($service->duration_minutes);

            // Check if slot start is in the past
            if ($slotStart->isPast()) {
                $unavailableSlots[] = $slot;
                continue;
            }

            foreach ($bookings as $booking) {
                $bookingStart = Carbon::parse($booking->scheduled_at)->timezone(config('app.timezone'));
                $bookingEnd = Carbon::parse($booking->ends_at)->timezone(config('app.timezone'));

                // Overlap check: slotStart < bookingEnd AND slotEnd > bookingStart
                if ($slotStart->lt($bookingEnd) && $slotEnd->gt($bookingStart)) {
                    $unavailableSlots[] = $slot;
                    break;
                }
            }
        }

        return array_values(array_unique($unavailableSlots));
    }

    public function getRichSlots(int $stylistId, int $serviceId, Carbon $date): array
    {
        $slots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];
        $unavailableSlots = $this->getUnavailableSlots($stylistId, $serviceId, $date);

        $richSlots = [];
        foreach ($slots as $slot) {
            $hour = (int) explode(':', $slot)[0];
            $period = $hour < 12 ? 'morning' : ($hour < 15 ? 'afternoon' : 'evening');

            $richSlots[] = [
                'time' => $slot,
                'label' => $slot,
                'period' => $period,
                'available' => !in_array($slot, $unavailableSlots),
            ];
        }

        return $richSlots;
    }
}
