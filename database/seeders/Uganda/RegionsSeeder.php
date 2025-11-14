<?php

namespace Database\Seeders\Uganda;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Country;

class RegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile  = database_path('seeders/Data/Uganda/Regions.json');
        $json = File::get($jsonFile);
        $data = json_decode($json, true);

        $country = Country::where('iso_code', 'UGA')->first();

        if (! $country) {
            return;
        }

        foreach ($data as $region) {
            $country->regions()->updateOrCreate(
                ['region_slug' => Str::slug($region['name'])],
                ['region_name' => $region['name']]
            );
        }
    }
}
