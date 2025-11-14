<?php

namespace Database\Factories;

use App\Models\Farmer;
use App\Models\MilkCollectionCenter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MilkDelivery>
 */
class MilkDeliveryFactory extends Factory
{
    public function definition(): array
    {
        $volume = $this->faker->randomFloat(2, 10, 200);
        $price = $this->faker->randomFloat(2, 500, 1500);

        return [
            'farmer_id' => Farmer::factory(),
            'milk_collection_center_id' => MilkCollectionCenter::factory(),
            'delivery_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'volume_liters' => $volume,
            'quality_grade' => $this->faker->optional()->randomElement(['A', 'B', 'C']),
            'fat_content' => $this->faker->optional()->randomFloat(2, 3, 6),
            'price_per_liter' => $price,
            'total_amount' => round($volume * $price, 2),
            'recorded_by' => $this->faker->name(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
