<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\BookingLog;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Stylist;
use App\Services\BookingAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private BookingAvailabilityService $availability
    ) {}

    public function create(Service $service): View
    {
        $stylists = $service->stylists()->active()->orderBy('name')->get();

        return view('bookings.create', compact('service', 'stylists'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'stylist_id' => ['required', 'exists:stylists,id'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:255'],
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

            // Booking Passport: find or create customer by phone
            $customer = Customer::findOrCreateFromBooking(
                $validated['customer_phone'],
                $validated['customer_name'],
                $validated['customer_email']
            );

            $booking = Booking::create([
                ...$validated,
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
                'scheduled_at' => $scheduledAt,
                'ends_at' => $this->availability->calculateEndsAt($service, $scheduledAt),
                'status' => 'pending',
            ]);

            // Update customer spending
            $customer->increment('total_spent', $service->price);
            if ($booking->stylist_id) {
                $customer->update(['preferred_stylist_id' => $booking->stylist_id]);
            }

            BookingLog::log($booking->id, 'created', 'Booking dibuat oleh pelanggan.');

            return $booking;
        });

        Mail::to($booking->customer_email)->queue(new BookingConfirmationMail($booking));

        return redirect()
            ->route('bookings.success', $booking)
            ->with('success', 'Booking berhasil dikirim! Tim kami akan mengonfirmasi via email.');
    }

    public function success(Booking $booking): View
    {
        $booking->load(['service', 'stylist']);

        return view('bookings.success', compact('booking'));
    }

    public function trackForm(): View
    {
        return view('bookings.track');
    }

    public function trackStatus(Request $request): View
    {
        $request->validate([
            'reference' => ['required', 'string', 'max:20'],
        ]);

        $reference = strtoupper(trim($request->get('reference')));
        $booking = Booking::with(['service', 'stylist'])
            ->where('reference', $reference)
            ->first();

        if (!$booking) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'reference' => ['Kode booking tidak ditemukan. Silakan periksa kembali.'],
            ]);
        }

        return view('bookings.track_status', compact('booking'));
    }

    public function cancelClient(Booking $booking): RedirectResponse
    {
        if (!$booking->canBeCancelledByClient()) {
            if ($booking->status !== 'pending') {
                return back()->withErrors(['error' => 'Hanya booking berstatus pending yang dapat dibatalkan.']);
            }
            return back()->withErrors(['error' => 'Pembatalan hanya bisa dilakukan minimal 2 jam sebelum jadwal.']);
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);
            BookingLog::log($booking->id, 'cancelled', 'Dibatalkan oleh pelanggan.');
        });

        return back()->with('success', 'Booking Anda berhasil dibatalkan.');
    }
}
