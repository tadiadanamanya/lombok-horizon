<?php

namespace Database\Factories;

use App\Models\Kavling;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kavling>
 */
class KavlingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'nomor' => $this->faker->randomElement(['A-001', 'A-002', 'A-003', 'B-001', 'B-002']), // or we can generate a pattern like $this->faker->regexify('[A-Z]-[0-9]{3}');
            'luas_m2' => 500, // fixed as per user example, but we can add variation: $this->faker->randomFloat(2, 400, 600),
            'harga' => 350000000, // fixed as per user example, but we can add variation: $this->faker->randomFloat(0, 300000000, 400000000),
            'status' => 'available', // fixed as per user example
            'koordinat_bidang' => json_encode([[$this->faker->longitude, $this->faker->latitude], [$this->faker->longitude, $this->faker->latitude], [$this->faker->longitude, $this->faker->latitude], [$this->faker->longitude, $this->faker->latitude]]),
        ];
    }
}
