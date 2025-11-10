<?php

namespace Database\Factories;

use App\Models\Subcounty;
use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parish>
 */
class ParishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Parish '.$this->faker->unique()->lexify('?????'),
            'subcounty_id' => Subcounty::factory(),
            'parish_code' => $this->faker->unique()->numerify('#######'),
            'slug' => fn (array $attributes) => SlugGenerator::generate($attributes['name'], 'parishes'),
        ];
    }
}
