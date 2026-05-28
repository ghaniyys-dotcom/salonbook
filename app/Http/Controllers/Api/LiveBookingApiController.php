<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LiveBookingApiController extends Controller
{
    public function stream(Request $request): StreamedResponse
    {
        return new StreamedResponse(function () {
            $lastCheckedId = Booking::max('id') ?? 0;

            // SSE Dev Loop — streams updates for a limited interval per request
            for ($i = 0; $i < 10; $i++) {
                $newBookings = Booking::with(['service', 'stylist'])
                    ->where('id', '>', $lastCheckedId)
                    ->get();

                if ($newBookings->isNotEmpty()) {
                    $lastCheckedId = $newBookings->max('id');
                    
                    foreach ($newBookings as $booking) {
                        echo "data: " . json_encode([
                            'id' => $booking->id,
                            'reference' => $booking->reference,
                            'customer_name' => $booking->customer_name,
                            'service_name' => $booking->service->name,
                            'stylist_name' => $booking->stylist->name ?? '-',
                            'price_formatted' => $booking->service->formattedPrice(),
                            'time_formatted' => $booking->scheduled_at->format('H:i'),
                        ]) . "\n\n";
                    }
                    ob_flush();
                    flush();
                }

                sleep(2);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function poll(Request $request)
    {
        $lastCheckedId = $request->query('last_checked_id');
        
        // If no last_checked_id, establish baseline by returning current max ID
        if ($lastCheckedId === null) {
            return response()->json([
                'bookings' => [],
                'last_checked_id' => Booking::max('id') ?? 0,
            ]);
        }

        $newBookings = Booking::with(['service', 'stylist'])
            ->where('id', '>', (int)$lastCheckedId)
            ->get()
            ->map(fn ($booking) => [
                'id' => $booking->id,
                'reference' => $booking->reference,
                'customer_name' => $booking->customer_name,
                'service_name' => $booking->service->name,
                'stylist_name' => $booking->stylist->name ?? '-',
                'price_formatted' => $booking->service->formattedPrice(),
                'time_formatted' => $booking->scheduled_at->format('H:i'),
            ]);

        return response()->json([
            'bookings' => $newBookings,
            'last_checked_id' => $newBookings->isNotEmpty() ? $newBookings->max('id') : (int)$lastCheckedId,
        ]);
    }
}
