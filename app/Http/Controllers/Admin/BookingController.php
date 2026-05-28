<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\BookingLog;
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

    public function index(Request $request): View
    {
        $query = Booking::with(['service', 'stylist'])->latest('scheduled_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('scheduled_at', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('scheduled_at', '<=', $to);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['service', 'stylist']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:'.implode(',', Booking::STATUSES)],
        ]);

        $oldStatus = $booking->status;

        // Validate status transitions
        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        $newStatus = $request->status;

        if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
            return back()->withErrors(['error' => "Tidak bisa mengubah status dari '{$oldStatus}' ke '{$newStatus}'."]);
        }

        DB::transaction(function () use ($request, $booking, $newStatus, $oldStatus) {
            if ($newStatus === 'confirmed' && $booking->status !== 'confirmed') {
                $this->availability->assertSlotAvailable(
                    $booking->stylist_id,
                    $booking->service_id,
                    Carbon::parse($booking->scheduled_at),
                    $booking->id
                );
            }

            $booking->update(['status' => $newStatus]);
            BookingLog::log($booking->id, $newStatus, "Status diubah oleh admin dari '{$oldStatus}' ke '{$newStatus}'.");

            if ($newStatus === 'confirmed') {
                Mail::to($booking->customer_email)->queue(new BookingConfirmationMail($booking));
            }
        });

        return back()->with('success', 'Status booking diperbarui.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);
            BookingLog::log($booking->id, 'cancelled', 'Booking dibatalkan oleh admin.');
        });

        return redirect()->route('admin.bookings.index')->with('success', 'Booking dibatalkan.');
    }

    public function kanban(): View
    {
        $bookings = Booking::with(['service', 'stylist'])
            ->whereDate('scheduled_at', '>=', now()->subDays(7))
            ->latest('scheduled_at')
            ->limit(200)
            ->get();
        
        $grouped = [
            'pending' => $bookings->where('status', 'pending'),
            'confirmed' => $bookings->where('status', 'confirmed'),
            'completed' => $bookings->where('status', 'completed'),
            'cancelled' => $bookings->where('status', 'cancelled'),
        ];

        return view('admin.bookings.kanban', compact('grouped'));
    }
}
