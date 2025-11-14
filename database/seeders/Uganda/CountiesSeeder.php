<?php

namespace Database\Seeders\Uganda;

use App\Models\County;
use App\Models\District;
use App\Support\SlugGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CountiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = database_path('seeders/Data/Uganda/Counties.json');
        $json = File::get($file);
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $district = District::where('district_code', $item['district'])->first();

            if (! $district) {
                continue;
            }

            $name = Str::title(Str::lower($item['name']));

            $county = County::firstOrNew(['county_code' => $item['id']]);
            $county->district_id = $district->id;
            $county->county_name = $name;
            $county->county_slug = SlugGenerator::generate($name, 'counties', $county->id);
            $county->save();
        }
    }
}