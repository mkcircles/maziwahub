<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CountyController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SubcountyController;
use App\Http\Controllers\ParishController;

Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('countries', CountryController::class);
    Route::apiResource('regions', RegionController::class);
    Route::apiResource('districts', DistrictController::class);
    Route::apiResource('counties', CountyController::class);
    Route::apiResource('subcounties', SubcountyController::class);
    Route::apiResource('parishes', ParishController::class);

    Route::get('countries/{country}/regions', [CountryController::class, 'regions']);
    Route::get('regions/{region}/districts', [RegionController::class, 'districts']);
    Route::get('districts/{district}/counties', [DistrictController::class, 'counties']);
    Route::get('counties/{county}/subcounties', [CountyController::class, 'subcounties']);
    Route::get('subcounties/{subcounty}/parishes', [SubcountyController::class, 'parishes']);
});
