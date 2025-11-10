<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        return DB::table($table)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }
}

