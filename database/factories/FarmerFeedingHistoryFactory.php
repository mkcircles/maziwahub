<?php

namespace Database\Factories;

use App\Models\Farmer;
use App\Models\FeedingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FarmerFeedingHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = Carbon::instance($this->faker->dateTimeBetween('-6 months', 'now'));

        return [
            'farmer_id' => Farmer::factory(),
            'feeding_method_id' => FeedingMethod::factory(),
            'feeding_type' => $this->faker->randomElement(['primary', 'supplemental']),
            'started_at' => $startedAt,
            'ended_at' => null,
            'notes' => $this->faker->optional()->sentence(),
            'metadata' => [
                'frequency_per_day' => $this->faker->numberBetween(1, 3),
                'supplements' => $this->faker->optional()->words(2, true),
            ],
            'recorded_by_id' => null,
        ];
    }
}


