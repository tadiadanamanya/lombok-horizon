<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Lombok',
            'slug' => Str::slug('Lombok-'.$this->faker->unique()->numberBetween(1, 999), '-'),
            'lokasi' => 'Lombok',
            'deskripsi' => $this->faker->paragraph,
            'thumbnail_path' => $this->faker->optional()->imageUrl(),
            'batas_proyek' => json_encode([[$this->faker->longitude, $this->faker->latitude], [$this->faker->longitude, $this->faker->latitude], [$this->faker->longitude, $this->faker->latitude]]),
        ];
    }
}
