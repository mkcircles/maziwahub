<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SlugGenerator
{
    public static function generate(string $name, string $table, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (self::slugExists($slug, $table, $ignoreId)) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected static function slugExists(string $slug, string $table, ?int $ignoreId = null): bool
    {

        if($table =='regions'){ $field_name = 'region_slug'; }
        elseif($table =='districts'){ $field_name = 'district_slug'; }
        elseif($table =='counties'){ $field_name = 'county_slug'; }
        elseif($table =='subcounties'){ $field_name = 'subcounty_slug'; }
        elseif($table =='parishes'){ $field_name = 'parish_slug'; }
        elseif($table =='villages'){ $field_name = 'village_slug'; }
        else{
            $field_name = 'slug';
        }
       
        Log::info($table.' => '.$field_name);

        return DB::table($table)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where($field_name, $slug)
            ->exists();
    }
}

