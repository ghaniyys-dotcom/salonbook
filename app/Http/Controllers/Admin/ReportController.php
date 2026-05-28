<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function export(Request $request): StreamedResponse
    {
        $query = Booking::with(['service', 'stylist'])->orderBy('scheduled_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('scheduled_at', '>=', $from);
        }

        if ($to = $request->get('to')) {
            $query->whereDate('scheduled_at', '<=', $to);
        }

        $bookings = $query->get();

        $filename = 'bookings-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Reference', 'Customer', 'Email', 'Phone', 'Service', 'Stylist',
                'Scheduled At', 'Status', 'Price',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    $booking->reference,
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->customer_phone,
                    $booking->service->name,
                    $booking->stylist->name,
                    $booking->scheduled_at->format('Y-m-d H:i'),
                    $booking->status,
                    $booking->service->price,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
