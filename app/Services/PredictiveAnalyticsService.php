<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PredictiveAnalyticsService
{
    /**
     * Calculate no-show risk score (0-100) for a booking.
     */
    public function calculateNoShowRisk(Booking $booking): int
    {
        $score = 0;

        // Factor 1: Days until appointment (further = higher risk)
        $daysUntil = now()->diffInDays($booking->scheduled_at, false);
        if ($daysUntil > 14) $score += 25;
        elseif ($daysUntil > 7) $score += 15;
        elseif ($daysUntil > 3) $score += 5;

        // Factor 2: Customer history — new customers have higher risk
        $customer = $booking->customer;
        if (!$customer || $customer->total_bookings <= 1) {
            $score += 30; // Brand new customer
        } elseif ($customer->total_bookings <= 3) {
            $score += 15;
        }

        // Factor 3: Previous no-show history
        if ($customer) {
            $noShowCount = Booking::where('customer_id', $customer->id)
                ->where('status', 'no_show')
                ->count();

            if ($noShowCount >= 3) $score += 40;
            elseif ($noShowCount >= 1) $score += 20;
        }

        // Factor 4: Booking time (early morning and late evening higher risk)
        $hour = $booking->scheduled_at->hour;
        if ($hour <= 9 || $hour >= 18) $score += 10;

        return min(100, $score);
    }

    /**
     * Generate booking heatmap data for a given week.
     * Returns a 7×11 grid (days × hour slots 09:00-19:00).
     */
    public function getHeatmapData(Carbon $weekStart): array
    {
        $heatmap = [];
        $slots = range(9, 19); // 09:00 to 19:00

        for ($day = 0; $day < 7; $day++) {
            $date = $weekStart->copy()->addDays($day);
            $dayData = [
                'day' => $date->format('D'),
                'date' => $date->format('d M'),
                'slots' => [],
            ];

            $bookings = Booking::whereDate('scheduled_at', $date)
                ->whereNotIn('status', ['cancelled'])
                ->get();

            foreach ($slots as $hour) {
                $slotStart = $date->copy()->setHour($hour)->setMinute(0);
                $slotEnd = $slotStart->copy()->addHour();

                $count = $bookings->filter(function ($b) use ($slotStart, $slotEnd) {
                    $bStart = Carbon::parse($b->scheduled_at);
                    $bEnd = Carbon::parse($b->ends_at);
                    return $bStart->lt($slotEnd) && $bEnd->gt($slotStart);
                })->count();

                $dayData['slots'][] = [
                    'hour' => sprintf('%02d:00', $hour),
                    'count' => $count,
                    'intensity' => $this->intensityLevel($count),
                ];
            }

            $heatmap[] = $dayData;
        }

        return $heatmap;
    }

    /**
     * Get stylist utilization for a specific date.
     * Returns percentage of working hours that are booked.
     */
    public function getStylistUtilization(Carbon $date): Collection
    {
        $totalWorkMinutes = 11 * 60; // 09:00-20:00 = 11 hours
        $stylists = Stylist::where('is_active', true)->get();

        return $stylists->map(function ($stylist) use ($date, $totalWorkMinutes) {
            $bookedMinutes = Booking::where('stylist_id', $stylist->id)
                ->whereDate('scheduled_at', $date)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->get()
                ->sum(function ($booking) {
                    return $booking->service ? $booking->service->duration_minutes : 0;
                });

            $utilization = round(($bookedMinutes / $totalWorkMinutes) * 100);

            return [
                'id' => $stylist->id,
                'name' => $stylist->name,
                'specialty' => $stylist->specialty,
                'booked_minutes' => $bookedMinutes,
                'utilization' => min(100, $utilization),
                'status' => $utilization >= 80 ? 'busy' : ($utilization >= 40 ? 'moderate' : 'idle'),
            ];
        });
    }

    /**
     * Forecast revenue for the next N days based on confirmed/pending bookings.
     */
    public function forecastRevenue(int $days = 7): array
    {
        $forecast = [];
        $total = 0;

        for ($i = 0; $i < $days; $i++) {
            $date = now()->addDays($i);
            $revenue = Booking::with('service')
                ->whereDate('scheduled_at', $date)
                ->whereIn('status', ['confirmed', 'pending'])
                ->get()
                ->sum(fn ($b) => $b->service->price ?? 0);

            $forecast[] = [
                'day' => $date->format('D'),
                'date' => $date->format('d M'),
                'revenue' => $revenue,
            ];
            $total += $revenue;
        }

        // Compare with last week
        $lastWeekRevenue = Booking::with('service')
            ->whereBetween('scheduled_at', [now()->subDays($days * 2), now()->subDays($days)])
            ->whereIn('status', ['confirmed', 'completed'])
            ->get()
            ->sum(fn ($b) => $b->service->price ?? 0);

        $delta = $lastWeekRevenue > 0
            ? round((($total - $lastWeekRevenue) / $lastWeekRevenue) * 100)
            : 0;

        return [
            'daily' => $forecast,
            'total' => $total,
            'last_period_total' => $lastWeekRevenue,
            'delta_percent' => $delta,
        ];
    }

    /**
     * Get customer retention funnel data.
     */
    public function getRetentionFunnel(): array
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('last_booking_at', '>=', now()->subDays(30))->count();
        $loyalCustomers = Customer::where('total_bookings', '>=', 5)->count();
        $atRiskCustomers = Customer::where('last_booking_at', '<', now()->subDays(30))
            ->where('total_bookings', '>', 0)
            ->count();

        return [
            'total' => $totalCustomers,
            'active' => $activeCustomers,
            'loyal' => $loyalCustomers,
            'at_risk' => $atRiskCustomers,
            'new_this_month' => Customer::where('first_booking_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    private function intensityLevel(int $count): string
    {
        return match (true) {
            $count >= 4 => 'high',
            $count >= 2 => 'medium',
            $count >= 1 => 'low',
            default => 'empty',
        };
    }
}
