<?php

namespace Database\Factories;

use App\Models\County;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subcounty>
 */
class SubcountyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Subcounty '.$this->faker->unique()->lexify('????'),
            'county_id' => County::factory(),
            'subcounty_code' => $this->faker->unique()->numerify('######'),
            'slug' => fn (array $attributes) => SlugGenerator::generate($attributes['name'], 'subcounties'),
        ];
    }
}
