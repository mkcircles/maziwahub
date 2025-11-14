<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\District;
use App\Models\County;
use App\Models\Subcounty;
use App\Models\Parish;
use App\Models\Village;
use App\Models\MilkCollectionCenter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get real geographical data
        $districts = District::all();
        $milkCenters = MilkCollectionCenter::all();

        if ($districts->isEmpty()) {
            $this->command->warn('No districts found. Please seed districts first.');
            return;
        }

        $this->command->info('Creating farmers...');

        // Create 50 farmers
        $count = 50;
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            // Get random district
            $district = $districts->random();
            
            // Get related geographical data
            $counties = County::where('district_id', $district->id)->get();
            $county = $counties->isNotEmpty() ? $counties->random() : null;
            
            $subcounties = $county 
                ? Subcounty::where('county_id', $county->id)->get() 
                : collect();
            $subcounty = $subcounties->isNotEmpty() ? $subcounties->random() : null;
            
            $parishes = $subcounty 
                ? Parish::where('subcounty_id', $subcounty->id)->get() 
                : collect();
            $parish = $parishes->isNotEmpty() ? $parishes->random() : null;
            
            $villages = $parish 
                ? Village::where('parish_id', $parish->id)->get() 
                : collect();
            $village = $villages->isNotEmpty() ? $villages->random() : null;

            // Get random milk collection center (optional)
            $milkCenter = $milkCenters->isNotEmpty() ? $milkCenters->random() : null;

            // Generate unique farmer ID
            $farmerId = $this->generateFarmerId();

            // Create farmer with real geographical data
            Farmer::factory()->create([
                'farmer_id' => $farmerId,
                'district' => $district->id,
                'county' => $county?->id,
                'sub_county' => $subcounty?->id,
                'parish' => $parish?->id,
                'village' => $village?->id,
                'milk_collection_center_id' => $milkCenter?->id,
                'status' => fake()->randomElement(['pending', 'active', 'inactive']),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Created {$count} farmers successfully!");
    }

    /**
     * Generate a unique 6-character alphanumeric farmer ID
     */
    protected function generateFarmerId(): string
    {
        do {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $farmerId = collect(range(1, 6))
                ->map(fn () => $characters[random_int(0, strlen($characters) - 1)])
                ->implode('');
        } while (Farmer::where('farmer_id', $farmerId)->exists());

        return $farmerId;
    }
}
