<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Support\SlugGenerator;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile  = database_path('seeders/Data/Countries.json');
        $json = File::get($jsonFile);
        $data = json_decode($json, true);

        foreach ($data as $country) {
            $model = Country::firstOrNew(['iso_code' => $country['iso_code']]);
            $model->name = $country['name'];
            $model->slug = SlugGenerator::generate($country['name'], 'countries', $model->id);
            $model->save();
        }
    }
}
