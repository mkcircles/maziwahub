<?php

namespace Database\Factories;

use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Country '.$this->faker->unique()->lexify('????'),
            'iso_code' => strtoupper($this->faker->unique()->lexify('???')),
            'slug' => fn (array $attributes) => SlugGenerator::generate($attributes['name'], 'countries'),
        ];
    }
}
