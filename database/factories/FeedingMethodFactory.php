<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeedingMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Pasture Grazing',
            'Cut-and-Carry',
            'Total Mixed Ration',
            'Silage Feeding',
            'Hay Feeding',
            'Zero Grazing',
        ]);

        return [
            'name' => $name,
            'code' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(100, 999),
            'category' => $this->faker->randomElement(['pasture', 'concentrate', 'mixed']),
            'description' => $this->faker->sentence(),
            'requires_details' => $this->faker->boolean(20),
            'is_active' => true,
            'metadata' => [
                'typical_frequency' => $this->faker->randomElement(['daily', 'twice daily', 'weekly']),
            ],
        ];
    }
}


