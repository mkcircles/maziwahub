<?php

namespace Database\Seeders\Uganda;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\District;
use App\Support\SlugGenerator;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districtFile = database_path('seeders/Data/Uganda/Districts.json');
        $districtJson = File::get($districtFile);
        $districtData = json_decode($districtJson, true);

        foreach ($districtData as $item) {
            $name = Str::title(Str::lower($item['name']));

            $district = District::firstOrNew(['district_code' => $item['id']]);
            $district->district_name = $name;
            $district->district_slug = SlugGenerator::generate($name, 'districts', $district->id);
            $district->region_id = 1;
            $district->save();
        }

        $regionFile = database_path('seeders/Data/Uganda/DistrictRegions.json');
        $regionJson = File::get($regionFile);
        $regionData = json_decode($regionJson, true);

        foreach ($regionData as $mapping) {
            $district = District::whereRaw('LOWER(district_name) = ?', [Str::lower($mapping['name'])])->first();

            if (! $district) {
                continue;
            }

            $district->update([
                'region_id' => $mapping['region_id'],
            ]);
        }
    }
}
