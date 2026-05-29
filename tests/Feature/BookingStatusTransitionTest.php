<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingLog;
use App\Models\Service;
use App\Models\Stylist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);
        Mail::fake();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);

        $service = Service::factory()->create(['duration_minutes' => 60, 'is_active' => true]);
        $stylist = Stylist::factory()->create(['is_active' => true]);
        $stylist->services()->sync([$service->id]);

        $scheduledAt = Carbon::tomorrow()->setHour(10)->setMinute(0)->setSecond(0);

        $this->booking = Booking::factory()->forStylistAndService($stylist, $service, $scheduledAt)
            ->pending()
            ->create();
    }

    // ─── Valid Transitions ───────────────────────────────────────

    public function test_admin_can_confirm_a_pending_booking(): void
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'confirmed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_admin_can_cancel_a_pending_booking(): void
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_admin_can_complete_a_confirmed_booking(): void
    {
        $this->actingAs($this->admin);
        $this->booking->update(['status' => 'confirmed']);

        $response = $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'completed',
        ]);
    }

    public function test_admin_can_cancel_a_confirmed_booking(): void
    {
        $this->actingAs($this->admin);
        $this->booking->update(['status' => 'confirmed']);

        $response = $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'cancelled',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_staff_can_update_booking_status(): void
    {
        $this->actingAs($this->staff);

        $response = $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'confirmed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'confirmed',
        ]);
    }

    // ─── Invalid Transitions ─────────────────────────────────────

    public function test_cannot_change_status_from_completed(): void
    {
        $this->actingAs($this->admin);
        $this->booking->update(['status' => 'completed']);

        foreach (['pending', 'confirmed', 'cancelled'] as $newStatus) {
            $response = $this->patch(route('admin.bookings.status', $this->booking), [
                'status' => $newStatus,
            ]);
            $this->assertTrue($response->isRedirect());
        }

        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'completed',
        ]);
    }

    public function test_cannot_change_status_from_cancelled(): void
    {
        $this->actingAs($this->admin);
        $this->booking->update(['status' => 'cancelled']);

        foreach (['pending', 'confirmed', 'completed'] as $newStatus) {
            $response = $this->patch(route('admin.bookings.status', $this->booking), [
                'status' => $newStatus,
            ]);
            $this->assertTrue($response->isRedirect());
        }

        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_change_from_pending_to_completed_directly(): void
    {
        $this->actingAs($this->admin);

        $response = $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'completed',
        ]);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'pending',
        ]);
    }

    // ─── Authorization ──────────────────────────────────────────

    public function test_unauthorized_user_cannot_update_booking_status(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $response = $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'confirmed',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'pending',
        ]);
    }

    // ─── Customer Cancellation ──────────────────────────────────

    public function test_customer_can_cancel_their_own_pending_booking(): void
    {
        $bookingId = $this->booking->id;
        $response = $this->post(route('bookings.cancel_client', $bookingId));

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $bookingId,
            'status' => 'cancelled',
        ]);
    }

    public function test_customer_cannot_cancel_non_pending_booking(): void
    {
        $this->booking->update(['status' => 'confirmed']);

        $response = $this->post(route('bookings.cancel_client', $this->booking->id));

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'confirmed',
        ]);
    }

    // ─── Double Booking Prevention on Confirm ───────────────────

    public function test_double_booking_is_prevented_when_confirming_conflicting_slot(): void
    {
        $this->actingAs($this->admin);

        $otherService = Service::factory()->create(['duration_minutes' => 60, 'is_active' => true]);
        $stylist = $this->booking->stylist;
        $stylist->services()->syncWithoutDetaching([$otherService->id]);

        $conflictingTime = Carbon::parse($this->booking->scheduled_at)->addMinutes(30);
        Booking::factory()->forStylistAndService($stylist, $otherService, $conflictingTime)
            ->pending()
            ->create();

        $response = $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'confirmed',
        ]);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'pending',
        ]);
    }

    // ─── Admin Destroy ──────────────────────────────────────────

    public function test_admin_delete_cancels_booking(): void
    {
        $this->actingAs($this->admin);

        $response = $this->delete(route('admin.bookings.destroy', $this->booking));

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'cancelled',
        ]);
    }

    // ─── Audit Log Tests ────────────────────────────────────────

    public function test_status_transition_creates_audit_log(): void
    {
        $this->actingAs($this->admin);

        $this->patch(route('admin.bookings.status', $this->booking), [
            'status' => 'confirmed',
        ]);

        $this->assertDatabaseHas('booking_logs', [
            'booking_id' => $this->booking->id,
            'action' => 'confirmed',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_customer_cancellation_creates_audit_log(): void
    {
        $bookingId = $this->booking->id;
        $this->post(route('bookings.cancel_client', $bookingId));

        $this->assertDatabaseHas('booking_logs', [
            'booking_id' => $bookingId,
            'action' => 'cancelled',
        ]);
    }

    public function test_admin_destroy_creates_audit_log(): void
    {
        $this->actingAs($this->admin);
        $bookingId = $this->booking->id;

        $this->delete(route('admin.bookings.destroy', $this->booking));

        $this->assertDatabaseHas('booking_logs', [
            'booking_id' => $bookingId,
            'action' => 'cancelled',
            'user_id' => $this->admin->id,
        ]);
    }
}
