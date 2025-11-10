<?php

namespace Database\Factories;

use App\Models\Parish;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Village>
 */
class VillageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Village '.$this->faker->unique()->lexify('??????'),
            'parish_id' => Parish::factory(),
            'village_code' => $this->faker->unique()->numerify('########'),
            'slug' => fn (array $attributes) => SlugGenerator::generate($attributes['name'], 'villages'),
        ];
    }
}
