<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MilkCollectionCenter>
 */
class MilkCollectionCenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $registrationNumber = $this->faker->unique()->numerify('REG-#####');

        if ($this->faker->boolean(30)) {
            $registrationNumber = null;
        }

        return [
            'name' => 'Center '.$this->faker->unique()->company(),
            'registration_number' => $registrationNumber,
            'physical_address' => $this->faker->address('Uganda'),
            'latitude' => $this->faker->optional()->latitude(),
            'longitude' => $this->faker->optional()->longitude(),
            'established_date' => $this->faker->optional()->date(),
            'manager_name' => $this->faker->optional()->name(),
            'manager_phone' => $this->faker->optional()->phoneNumber(),
            'staff_count' => $this->faker->numberBetween(0, 50),
            'power_source' => $this->faker->optional()->randomElement(['Grid', 'Generator', 'Solar']),
            'cooler_capacity_liters' => $this->faker->optional()->numberBetween(500, 5000),
            'has_testing_equipment' => $this->faker->boolean(),
            'has_washing_bay' => $this->faker->boolean(),
        ];
    }
}
