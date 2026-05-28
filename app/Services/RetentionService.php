<?php

namespace App\Services;

use App\Models\AutomatedMessage;
use App\Models\Booking;
use App\Models\Customer;
use Carbon\Carbon;

class RetentionService
{
    /**
     * Schedule a 24h reminder for upcoming bookings.
     */
    public function scheduleReminders(): int
    {
        $count = 0;
        $upcoming = Booking::where('status', 'confirmed')
            ->whereBetween('scheduled_at', [now()->addHours(23), now()->addHours(25)])
            ->whereDoesntHave('customer', function ($q) {
                // Skip if already has a reminder scheduled
            })
            ->get();

        foreach ($upcoming as $booking) {
            $exists = AutomatedMessage::where('booking_id', $booking->id)
                ->where('type', 'reminder')
                ->exists();

            if (!$exists) {
                AutomatedMessage::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'type' => 'reminder',
                    'scheduled_at' => $booking->scheduled_at->copy()->subDay(),
                    'channel' => 'whatsapp',
                    'status' => 'pending',
                    'message_content' => $this->buildReminderMessage($booking),
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Schedule thank-you messages for completed bookings.
     */
    public function scheduleThankYou(): int
    {
        $count = 0;
        $completed = Booking::where('status', 'completed')
            ->whereBetween('updated_at', [now()->subHours(3), now()->subHours(1)])
            ->get();

        foreach ($completed as $booking) {
            $exists = AutomatedMessage::where('booking_id', $booking->id)
                ->where('type', 'thank_you')
                ->exists();

            if (!$exists) {
                AutomatedMessage::create([
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'type' => 'thank_you',
                    'scheduled_at' => now(),
                    'channel' => 'whatsapp',
                    'status' => 'pending',
                    'message_content' => $this->buildThankYouMessage($booking),
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Schedule re-booking nudges for customers who haven't visited in 30+ days.
     */
    public function scheduleRebookingNudges(): int
    {
        $count = 0;
        $atRiskCustomers = Customer::where('total_bookings', '>', 0)
            ->where('last_booking_at', '<', now()->subDays(30))
            ->get();

        foreach ($atRiskCustomers as $customer) {
            // Don't send more than once per 30-day period
            $recentNudge = AutomatedMessage::where('customer_id', $customer->id)
                ->where('type', 'rebooking')
                ->where('created_at', '>=', now()->subDays(30))
                ->exists();

            if (!$recentNudge) {
                AutomatedMessage::create([
                    'customer_id' => $customer->id,
                    'type' => 'rebooking',
                    'scheduled_at' => now(),
                    'channel' => 'whatsapp',
                    'status' => 'pending',
                    'message_content' => $this->buildRebookingMessage($customer),
                ]);
                $count++;
            }
        }

        return $count;
    }

    private function buildReminderMessage(Booking $booking): string
    {
        $booking->load(['service', 'stylist']);
        $date = $booking->scheduled_at->timezone(config('app.timezone'))->format('d M Y');
        $time = $booking->scheduled_at->timezone(config('app.timezone'))->format('H:i');

        return "Halo {$booking->customer_name}! 👋\n\n"
            . "Mengingatkan jadwal perawatan *{$booking->service->name}* Anda besok:\n\n"
            . "📅 {$date}\n"
            . "⏰ {$time} WIB\n"
            . "💇 Stylist: {$booking->stylist->name}\n\n"
            . "Sampai ketemu di *Glow Studio*! ✨";
    }

    private function buildThankYouMessage(Booking $booking): string
    {
        $booking->load('service');

        return "Terima kasih {$booking->customer_name}! 🌟\n\n"
            . "Kami harap perawatan *{$booking->service->name}* hari ini memuaskan.\n\n"
            . "Jika Anda puas, kami sangat menghargai review di Google Maps kami:\n"
            . "🔗 https://g.page/glow-studio-jakarta/review\n\n"
            . "Sampai jumpa kembali di *Glow Studio*! 💛";
    }

    private function buildRebookingMessage(Customer $customer): string
    {
        $favoriteService = $customer->favoriteService();
        $serviceName = $favoriteService ? $favoriteService->name : 'treatment favorit Anda';

        return "Hai {$customer->name}! 💛\n\n"
            . "Sudah lama kami tidak melihat Anda. Kangen nih! 😊\n\n"
            . "Siap untuk sesi *{$serviceName}* berikutnya?\n"
            . "Booking sekarang dan dapatkan pengalaman premium yang Anda rindukan.\n\n"
            . "🔗 " . url('/') . "\n\n"
            . "— Tim *Glow Studio* ✨";
    }

    /**
     * Process and "send" all pending due automated messages.
     */
    public function processPendingMessages(): int
    {
        $messages = AutomatedMessage::due()->get();
        $count = 0;
        foreach ($messages as $message) {
            // Simulate sending message via WhatsApp / Email
            $message->markSent();
            $count++;
        }
        return $count;
    }
}
