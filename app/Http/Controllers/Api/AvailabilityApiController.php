<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityApiController extends Controller
{
    public function __construct(
        private BookingAvailabilityService $availability
    ) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'stylist_id' => ['required', 'exists:stylists,id'],
            'service_id' => ['required', 'exists:services,id'],
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        $date = Carbon::parse($validated['date'])->timezone(config('app.timezone'));

        $unavailableSlots = $this->availability->getUnavailableSlots(
            (int) $validated['stylist_id'],
            (int) $validated['service_id'],
            $date
        );

        return response()->json([
            'unavailable_slots' => $unavailableSlots,
            'slots' => $this->availability->getRichSlots(
                (int) $validated['stylist_id'],
                (int) $validated['service_id'],
                $date
            ),
        ]);
    }
}
