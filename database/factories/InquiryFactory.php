<?php

namespace Database\Factories;

use App\Models\Inquiry;
use App\Models\Kavling;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'kavling_id' => Kavling::factory(),
            'project_id' => Project::factory(),
            'ref_code' => 'LH-'.$this->faker->year.'-'.$this->faker->unique()->numberBetween(1, 9999),
            'status' => $this->faker->randomElement(['baru', 'dihubungi', 'deal', 'ditolak']),
        ];
    }
}
