<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Kavling;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kavling_id' => Kavling::factory(),
            'user_id' => User::factory(),
            'buyer_name' => $this->faker->name(),
            'buyer_phone' => $this->faker->phoneNumber(),
            'buyer_email' => $this->faker->optional()->safeEmail,
            'booking_fee' => $this->faker->randomFloat(2, 0, 5000000),
            'deal_price' => $this->faker->randomFloat(0, 200000000, 1000000000),
            'status' => $this->faker->randomElement(['pending', 'verified', 'cancelled']),
            'booked_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'verified_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
