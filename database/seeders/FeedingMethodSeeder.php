<?php

namespace Database\Seeders;

use App\Models\FeedingMethod;
use Illuminate\Database\Seeder;

class FeedingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Pasture Grazing',
                'code' => 'PASTURE_GRAZING',
                'category' => 'pasture',
                'description' => 'Open grazing on natural or improved pastures.',
                'requires_details' => false,
                'metadata' => [
                    'typical_frequency' => 'Continuous',
                    'suitable_for' => ['smallholder', 'medium'],
                ],
            ],
            [
                'name' => 'Zero Grazing',
                'code' => 'ZERO_GRAZING',
                'category' => 'cut-and-carry',
                'description' => 'Cut-and-carry fodder brought to animals in housing.',
                'requires_details' => true,
                'metadata' => [
                    'housing_required' => true,
                    'labour_intensity' => 'high',
                ],
            ],
            [
                'name' => 'Total Mixed Ration (TMR)',
                'code' => 'TOTAL_MIXED_RATION',
                'category' => 'mixed',
                'description' => 'Balanced ration composed of forages, concentrates, and supplements.',
                'requires_details' => true,
                'metadata' => [
                    'requires_mixer' => true,
                    'typical_frequency' => '2-3 feeds/day',
                ],
            ],
            [
                'name' => 'Silage Feeding',
                'code' => 'SILAGE_FEEDING',
                'category' => 'preserved',
                'description' => 'Fermented forage stored in silos or bags.',
                'requires_details' => false,
                'metadata' => [
                    'storage' => ['silo', 'silage bag'],
                    'moisture_target' => '60-70%',
                ],
            ],
            [
                'name' => 'Hay Feeding',
                'code' => 'HAY_FEEDING',
                'category' => 'preserved',
                'description' => 'Dried forage supplied during scarcity.',
                'requires_details' => false,
                'metadata' => [
                    'storage' => ['bales', 'stack'],
                    'typical_frequency' => 'Seasonal',
                ],
            ],
            [
                'name' => 'Concentrate Supplementation',
                'code' => 'CONCENTRATE_SUPPLEMENT',
                'category' => 'supplemental',
                'description' => 'High-energy or protein concentrates provided alongside forage.',
                'requires_details' => true,
                'metadata' => [
                    'examples' => ['dairy meal', 'cotton seed cake'],
                    'typical_frequency' => 'Twice daily',
                ],
            ],
        ];

        foreach ($methods as $method) {
            FeedingMethod::updateOrCreate(
                ['code' => $method['code']],
                array_merge($method, ['is_active' => true])
            );
        }
    }
}
