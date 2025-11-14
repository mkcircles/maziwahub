<?php

namespace Database\Factories;

use App\Models\Cow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CowMilkProduction>
 */
class CowMilkProductionFactory extends Factory
{
    public function definition(): array
    {
        $morning = $this->faker->randomFloat(2, 0, 20);
        $midday = $this->faker->randomFloat(2, 0, 20);
        $evening = $this->faker->randomFloat(2, 0, 20);
        $total = $morning + $midday + $evening;

        return [
            'cow_id' => Cow::factory(),
            'recorded_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'morning_volume_liters' => $morning,
            'midday_volume_liters' => $midday,
            'evening_volume_liters' => $evening,
            'total_volume_liters' => round($total, 2),
            'recorded_by' => $this->faker->name(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
