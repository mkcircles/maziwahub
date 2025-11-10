<?php

namespace Database\Seeders\Uganda;

use App\Models\County;
use App\Models\Subcounty;
use App\Support\SlugGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SubCountiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile = database_path('seeders/Data/Uganda/SubCounties.json');
        $json = File::get($jsonFile);
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $county = County::where('county_code', $item['county'])->first();

            if (! $county) {
                continue;
            }

            $name = Str::title(Str::lower($item['name']));

            $subcounty = Subcounty::firstOrNew(['subcounty_code' => $item['id']]);
            $subcounty->name = $name;
            $subcounty->county_id = $county->id;
            $subcounty->slug = SlugGenerator::generate($name, 'subcounties', $subcounty->id);
            $subcounty->save();
        }
    }
}
