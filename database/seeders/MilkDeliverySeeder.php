<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MilkDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating milk deliveries for the past 30 days...');

        $farmers = \App\Models\Farmer::all();
        $mccs = \App\Models\MilkCollectionCenter::all();

        if ($farmers->isEmpty() || $mccs->isEmpty()) {
            $this->command->warn('No farmers or MCCs found. Please seed them first.');
            return;
        }

        $startDate = now()->subDays(30)->startOfDay();
        $days = 30;

        $bar = $this->command->getOutput()->createProgressBar($days);
        $bar->start();

        for ($i = $days; $i >= 0; $i--) {
            $currentDate = now()->subDays($i)->startOfDay();

            // Random number of deliveries per day (between 10 and 30)
            $dailyDeliveriesCount = rand(10, 30);

            for ($j = 0; $j < $dailyDeliveriesCount; $j++) {
                $farmer = $farmers->random();
                $mcc = $farmer->milkCollectionCenter ?? $mccs->random();

                // Add random hours to the delivery date for realism
                $deliveryDate = $currentDate->copy()->addHours(rand(6, 18))->addMinutes(rand(0, 59));

                \App\Models\MilkDelivery::factory()->create([
                    'farmer_id' => $farmer->id,
                    'milk_collection_center_id' => $mcc->id,
                    'delivery_date' => $deliveryDate,
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Created random milk deliveries for the past {$days} days successfully!");
    }
}
