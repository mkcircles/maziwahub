<?php

namespace Database\Seeders;

use App\Models\MilkCollectionCenter;
use App\Models\Village;
use Illuminate\Database\Seeder;

class MilkCollectionCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villages = Village::with('parish.subcounty.county.district.region.country')
            ->inRandomOrder()
            ->limit(50)
            ->get();

        MilkCollectionCenter::factory()
            ->count(10)
            ->create()
            ->each(function (MilkCollectionCenter $center) use ($villages): void {
                $village = $villages->isNotEmpty() ? $villages->random() : null;

                if (! $village) {
                    return;
                }

                $center->update([
                    'location' => [
                        'country_id' => optional($village->parish?->subcounty?->county?->district?->region?->country)->id,
                        'region_id' => optional($village->parish?->subcounty?->county?->district?->region)->id,
                        'district_id' => optional($village->parish?->subcounty?->county?->district)->id,
                        'county_id' => optional($village->parish?->subcounty?->county)->id,
                        'subcounty_id' => optional($village->parish?->subcounty)->id,
                        'parish_id' => optional($village->parish)->id,
                        'village_id' => $village->id,
                    ],
                ]);
            });
    }
}
