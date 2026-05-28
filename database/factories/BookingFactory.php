<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Stylist;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $service = Service::factory()->create();
        $stylist = Stylist::factory()->create();
        $scheduledAt = Carbon::tomorrow()->setHour(10)->setMinute(0)->setSecond(0);

        // Attach stylist to service
        $stylist->services()->syncWithoutDetaching([$service->id]);

        return [
            'service_id' => $service->id,
            'stylist_id' => $stylist->id,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'scheduled_at' => $scheduledAt,
            'ends_at' => $scheduledAt->copy()->addMinutes($service->duration_minutes),
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'confirmed']);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'completed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'cancelled']);
    }

    public function forStylistAndService(Stylist $stylist, Service $service, Carbon $scheduledAt): static
    {
        return $this->state(fn (array $attributes) => [
            'stylist_id' => $stylist->id,
            'service_id' => $service->id,
            'scheduled_at' => $scheduledAt,
            'ends_at' => $scheduledAt->copy()->addMinutes($service->duration_minutes),
        ]);
    }
}
