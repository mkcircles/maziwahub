<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\Uganda\RegionsSeeder as UgandaRegionsSeeder;
use Database\Seeders\Uganda\DistrictSeeder as UgandaDistrictsSeeder;
use Database\Seeders\Uganda\CountiesSeeder as UgandaCountiesSeeder;
use Database\Seeders\Uganda\SubCountiesSeeder as UgandaSubCountiesSeeder;
use Database\Seeders\Uganda\ParishesSeeder as UgandaParishesSeeder;
use Database\Seeders\Uganda\VillagesSeeder as UgandaVillagesSeeder;
use Database\Seeders\MilkCollectionCenterSeeder;
use Database\Seeders\FarmerSeeder;
use Database\Seeders\FeedingMethodSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\VetSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            //Uganda Data Seeding
            UgandaRegionsSeeder::class,
            UgandaDistrictsSeeder::class,
            UgandaCountiesSeeder::class,
            UgandaSubCountiesSeeder::class,
            UgandaParishesSeeder::class,
            UgandaVillagesSeeder::class,
            MilkCollectionCenterSeeder::class,
            FeedingMethodSeeder::class,
            FarmerSeeder::class,
            VetSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
