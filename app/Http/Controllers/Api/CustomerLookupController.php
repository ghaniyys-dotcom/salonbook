<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerLookupController extends Controller
{
    /**
     * Lookup a customer by phone number for auto-fill in booking form.
     */
    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:8', 'max:20'],
        ]);

        $cleanPhone = preg_replace('/\D/', '', $validated['phone']);

        $customer = Customer::where('phone', $cleanPhone)->first();

        if (!$customer) {
            return response()->json([
                'found' => false,
                'message' => 'Pelanggan baru — selamat datang di Glow Studio!',
            ]);
        }

        return response()->json([
            'found' => true,
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
                'visit_count' => $customer->total_bookings,
                'total_spent' => $customer->formattedTotalSpent(),
                'preferred_stylist_id' => $customer->preferred_stylist_id,
                'is_loyal' => $customer->isLoyal(),
                'last_visit' => $customer->last_booking_at?->diffForHumans(),
            ],
            'message' => "Selamat datang kembali, {$customer->name}! Kunjungan ke-" . ($customer->total_bookings + 1) . " Anda.",
        ]);
    }
}
