<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\BookingLog;
use App\Models\Service;
use App\Services\BookingAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingApiController extends Controller
{
    public function __construct(
        private BookingAvailabilityService $availability
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->integer('per_page', 15), 100);

        $bookings = Booking::with(['service', 'stylist'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($perPage);

        return response()->json($bookings);
    }

    public function show(Booking $booking): JsonResponse
    {
        $booking->load(['service', 'stylist']);

        return response()->json(['data' => $booking]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'stylist_id' => ['required', 'exists:stylists,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $scheduledAt = Carbon::parse($validated['scheduled_at'])->timezone(config('app.timezone'));

        $booking = DB::transaction(function () use ($validated, $service, $scheduledAt) {
            $this->availability->assertSlotAvailable(
                (int) $validated['stylist_id'],
                (int) $validated['service_id'],
                $scheduledAt
            );

            $booking = Booking::create([
                ...$validated,
                'scheduled_at' => $scheduledAt,
                'ends_at' => $this->availability->calculateEndsAt($service, $scheduledAt),
                'status' => 'pending',
            ]);

            BookingLog::log($booking->id, 'created', 'Booking dibuat via API.');

            return $booking;
        });

        Mail::to($booking->customer_email)->queue(new BookingConfirmationMail($booking));

        return response()->json(['data' => $booking->load(['service', 'stylist'])], 201);
    }
}
