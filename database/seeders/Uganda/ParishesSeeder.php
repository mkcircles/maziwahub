<?php

namespace Database\Seeders\Uganda;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Parish;
use App\Models\Subcounty;
use App\Support\SlugGenerator;

class ParishesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile = database_path('seeders/Data/Uganda/Parishes.json');
        $json = File::get($jsonFile);
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $subcounty = Subcounty::where('subcounty_code', $item['subcounty'])->first();

            if (! $subcounty) {
                continue;
            }

            $name = Str::title(Str::lower($item['name']));

            $parish = Parish::where('parish_code', $item['id'])->first();

            if (! $parish) {
                $parish = Parish::firstOrNew([
                    'subcounty_id' => $subcounty->id,
                    'name' => $name,
                ]);
            }

            if ($parish->exists && $parish->subcounty_id === $subcounty->id && $parish->name === $name && $parish->parish_code !== null && $parish->parish_code !== $item['id']) {
                continue;
            }

            $parish->name = $name;
            $parish->subcounty_id = $subcounty->id;
            $parish->parish_code = $item['id'];

            $parish->slug = SlugGenerator::generate($name, 'parishes', $parish->exists ? $parish->id : null);
            $parish->save();
        }
    }
}
