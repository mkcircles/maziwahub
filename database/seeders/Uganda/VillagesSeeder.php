<?php

namespace Database\Seeders\Uganda;

use App\Models\Parish;
use App\Models\Village;
use App\Support\SlugGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VillagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile = database_path('seeders/Data/Uganda/Villages.json');
        $json = File::get($jsonFile);
        $data = json_decode($json, true);

        foreach ($data as $item) {
           
            $parish = Parish::where('parish_code', $item['parish'])->first();

            if (! $parish) {
                continue;
            }

            $name = Str::title(Str::lower($item['name']));

            $village = Village::firstOrNew(['village_code' => $item['id']]);
            $village->village_name = $name;
            $village->parish_id = $parish->id;
            $village->village_code = $item['id'];
            $village->village_slug = SlugGenerator::generate($name, 'villages', $village->id);
            $village->save();
        }
    }
}
