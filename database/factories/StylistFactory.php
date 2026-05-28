<?php

namespace Database\Factories;

use App\Models\Stylist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stylist>
 */
class StylistFactory extends Factory
{
    protected $model = Stylist::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'specialty' => fake()->randomElement([
                'Hair & Color', 'Men Grooming', 'Nail Art',
                'Facial Specialist', 'Bridal Stylist', 'Hair Treatment',
            ]),
            'bio' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
