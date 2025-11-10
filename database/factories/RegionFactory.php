<?php

namespace Database\Factories;

use App\Models\Country;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Region '.$this->faker->unique()->lexify('????'),
            'country_id' => Country::factory(),
            'slug' => fn (array $attributes) => SlugGenerator::generate($attributes['name'], 'regions'),
        ];
    }
}
