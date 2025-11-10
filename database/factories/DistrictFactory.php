<?php

namespace Database\Factories;

use App\Models\Region;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\District>
 */
class DistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'District '.$this->faker->unique()->lexify('????'),
            'region_id' => Region::factory(),
            'district_code' => $this->faker->unique()->numerify('####'),
            'slug' => fn (array $attributes) => SlugGenerator::generate($attributes['name'], 'districts'),
        ];
    }
}
