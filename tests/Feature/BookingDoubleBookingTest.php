<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingDoubleBookingTest extends TestCase
{
    use RefreshDatabase;

    private Service $service;
    private Stylist $stylist;
    private Carbon $baseTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
        ]);

        Mail::fake();

        $this->service = Service::factory()->create([
            'duration_minutes' => 60,
            'is_active' => true,
        ]);
        $this->stylist = Stylist::factory()->create(['is_active' => true]);
        $this->stylist->services()->sync([$this->service->id]);

        $this->baseTime = Carbon::tomorrow()->setHour(10)->setMinute(0)->setSecond(0)->setMicrosecond(0);
    }

    private function bookingPayload(array $overrides = []): array
    {
        return array_merge([
            'service_id' => $this->service->id,
            'stylist_id' => $this->stylist->id,
            'customer_name' => 'Test Customer',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '081234567890',
            'scheduled_at' => $this->baseTime->toDateTimeString(),
            'notes' => 'Test notes',
        ], $overrides);
    }

    public function test_a_booking_can_be_created_successfully_via_web(): void
    {
        $response = $this->post(route('bookings.store'), $this->bookingPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'customer_email' => 'customer@example.com',
            'stylist_id' => $this->stylist->id,
            'status' => 'pending',
        ]);
    }

    public function test_a_booking_can_be_created_successfully_via_api(): void
    {
        $response = $this->postJson('/api/v1/bookings', $this->bookingPayload());

        $response->assertCreated();
        $response->assertJsonPath('data.status', 'pending');
        $response->assertJsonPath('data.customer_email', 'customer@example.com');
    }

    public function test_cannot_book_same_stylist_at_overlapping_time_via_web(): void
    {
        Booking::factory()->forStylistAndService(
            $this->stylist, $this->service, $this->baseTime
        )->create(['status' => 'confirmed']);

        $overlapTime = $this->baseTime->copy()->addMinutes(30);
        $response = $this->post(route('bookings.store'), $this->bookingPayload([
            'scheduled_at' => $overlapTime->toDateTimeString(),
        ]));

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_cannot_book_same_stylist_at_overlapping_time_via_api(): void
    {
        Booking::factory()->forStylistAndService(
            $this->stylist, $this->service, $this->baseTime
        )->create(['status' => 'confirmed']);

        $overlapTime = $this->baseTime->copy()->addMinutes(30);
        $response = $this->postJson('/api/v1/bookings', $this->bookingPayload([
            'scheduled_at' => $overlapTime->toDateTimeString(),
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('scheduled_at');
    }

    public function test_cannot_book_when_new_booking_partially_overlaps_existing_slot_start(): void
    {
        Booking::factory()->forStylistAndService(
            $this->stylist, $this->service, $this->baseTime
        )->create(['status' => 'confirmed']);

        // New booking at 09:30-10:30 overlaps existing at 10:00-11:00
        $start = $this->baseTime->copy()->subMinutes(30);
        $response = $this->post(route('bookings.store'), $this->bookingPayload([
            'scheduled_at' => $start->toDateTimeString(),
        ]));

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_cannot_book_when_new_booking_fully_contains_existing_slot(): void
    {
        $shortService = Service::factory()->create([
            'duration_minutes' => 30, 'is_active' => true,
        ]);
        $this->stylist->services()->syncWithoutDetaching([$shortService->id]);

        // Existing booking at 10:00-10:30
        Booking::factory()->forStylistAndService(
            $this->stylist, $shortService, $this->baseTime
        )->create(['status' => 'confirmed']);

        // New booking at 09:30-11:30 fully contains existing slot
        $start = $this->baseTime->copy()->subMinutes(30);
        $response = $this->post(route('bookings.store'), $this->bookingPayload([
            'scheduled_at' => $start->toDateTimeString(),
        ]));

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_can_book_non_overlapping_slot_for_same_stylist(): void
    {
        Booking::factory()->forStylistAndService(
            $this->stylist, $this->service, $this->baseTime
        )->create(['status' => 'confirmed']);

        // Book at 11:00 — non-overlapping (existing ends at 11:00, new starts at 11:00)
        $nonOverlapTime = $this->baseTime->copy()->addHours(1);
        $response = $this->post(route('bookings.store'), $this->bookingPayload([
            'scheduled_at' => $nonOverlapTime->toDateTimeString(),
        ]));

        $response->assertRedirect();
        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_can_book_same_time_for_different_stylist(): void
    {
        $anotherStylist = Stylist::factory()->create(['is_active' => true]);
        $anotherStylist->services()->sync([$this->service->id]);

        Booking::factory()->forStylistAndService(
            $this->stylist, $this->service, $this->baseTime
        )->create(['status' => 'confirmed']);

        $response = $this->post(route('bookings.store'), $this->bookingPayload([
            'stylist_id' => $anotherStylist->id,
        ]));

        $response->assertRedirect();
        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_cancelled_bookings_do_not_block_availability(): void
    {
        Booking::factory()->forStylistAndService(
            $this->stylist, $this->service, $this->baseTime
        )->create(['status' => 'cancelled']);

        $response = $this->post(route('bookings.store'), $this->bookingPayload());

        $response->assertRedirect();
        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_cannot_book_inactive_service(): void
    {
        $inactiveService = Service::factory()->inactive()->create();
        $this->stylist->services()->syncWithoutDetaching([$inactiveService->id]);

        $response = $this->post(route('bookings.store'), $this->bookingPayload([
            'service_id' => $inactiveService->id,
        ]));

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('bookings', ['service_id' => $inactiveService->id]);
    }

    public function test_cannot_book_inactive_stylist(): void
    {
        $inactiveStylist = Stylist::factory()->inactive()->create();
        $inactiveStylist->services()->sync([$this->service->id]);

        $response = $this->post(route('bookings.store'), $this->bookingPayload([
            'stylist_id' => $inactiveStylist->id,
        ]));

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('bookings', ['stylist_id' => $inactiveStylist->id]);
    }

    public function test_cannot_book_in_the_past(): void
    {
        $pastTime = Carbon::now()->subDay();
        $response = $this->post(route('bookings.store'), $this->bookingPayload([
            'scheduled_at' => $pastTime->toDateTimeString(),
        ]));

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_api_rate_limit_blocks_excessive_requests(): void
    {
        $this->markTestSkipped(
            'Rate limiting requires a persistent cache (e.g., file/redis). Skipping in array cache mode.'
        );
    }
}
