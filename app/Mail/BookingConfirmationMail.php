<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Booking — '.$this->booking->reference.' | Glow Studio',
        );
    }

    public function content(): Content
    {
        $this->booking->load(['service', 'stylist']);

        return new Content(
            markdown: 'emails.booking-confirmation',
        );
    }
}
