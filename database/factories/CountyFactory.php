<?php

namespace Database\Factories;

use App\Models\District;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\County>
 */
class CountyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'County '.$this->faker->unique()->lexify('????'),
            'district_id' => District::factory(),
            'county_code' => $this->faker->unique()->numerify('#####'),
            'slug' => fn (array $attributes) => SlugGenerator::generate($attributes['name'], 'counties'),
        ];
    }
}
