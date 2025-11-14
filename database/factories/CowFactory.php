<?php

namespace Database\Factories;

use App\Models\Farmer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cow>
 */
class CowFactory extends Factory
{
    public function definition(): array
    {
        return [
            'farmer_id' => Farmer::factory(),
            'tag_number' => 'TAG-'.$this->faker->unique()->numerify('#####'),
            'breed' => $this->faker->randomElement(['Friesian', 'Ankole', 'Guernsey', 'Jersey']),
            'date_of_birth' => $this->faker->optional()->dateTimeBetween('-8 years', '-1 year'),
            'acquired_date' => $this->faker->optional()->dateTimeBetween('-5 years', 'now'),
            'milk_capacity_liters' => $this->faker->optional()->randomFloat(2, 5, 30),
            'health_status' => $this->faker->optional()->randomElement(['healthy', 'under treatment', 'recovering']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
