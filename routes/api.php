<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CountyController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SubcountyController;
use App\Http\Controllers\ParishController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\MilkCollectionCenterController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\CowController;
use App\Http\Controllers\MilkDeliveryController;
use App\Http\Controllers\CowMilkProductionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedingMethodController;
use App\Http\Controllers\FarmerFeedingHistoryController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerInvitationController;
use App\Http\Controllers\MilkCollectionCenterClaimController;

Route::prefix('v1')->group(function () {
   

    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::post('partner-invitations/{token}/accept', [PartnerInvitationController::class, 'accept']);

    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::get('auth/user', [AuthController::class, 'user']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Geography routes - accessible to all authenticated users (view-only for 'user' role)
    
            Route::apiResource('countries', CountryController::class);
            Route::apiResource('regions', RegionController::class);
            Route::apiResource('districts', DistrictController::class);
            Route::apiResource('counties', CountyController::class);
            Route::apiResource('subcounties', SubcountyController::class);
            Route::apiResource('parishes', ParishController::class);
            Route::apiResource('villages', VillageController::class);

            Route::get('countries/{country}/regions', [CountryController::class, 'regions']);
            Route::get('countries/{country}/farmers', [CountryController::class, 'farmers']);
            Route::get('regions/{region}/districts', [RegionController::class, 'districts']);
            Route::get('districts/{district}/counties', [DistrictController::class, 'counties']);
            Route::get('counties/{county}/subcounties', [CountyController::class, 'subcounties']);
            Route::get('subcounties/{subcounty}/parishes', [SubcountyController::class, 'parishes']);
            Route::get('parishes/{parish}/villages', [ParishController::class, 'villages']);

            // Milk Collection Centers - with policy checks
            Route::apiResource('milk-collection-centers', MilkCollectionCenterController::class);
            Route::get('milk-collection-center-claims', [MilkCollectionCenterClaimController::class, 'index']);
            Route::post('milk-collection-centers/{milkCollectionCenter}/claims', [MilkCollectionCenterClaimController::class, 'store']);
            Route::post('milk-collection-center-claims/{claim}/approve', [MilkCollectionCenterClaimController::class, 'approve']);
            Route::post('milk-collection-center-claims/{claim}/reject', [MilkCollectionCenterClaimController::class, 'reject']);

            // Farmers - with policy checks
            Route::apiResource('farmers', FarmerController::class);
            Route::apiResource('feeding-methods', FeedingMethodController::class);
            Route::get('farmers/{farmer}/feeding-history', [FarmerFeedingHistoryController::class, 'index']);
            Route::post('farmers/{farmer}/feeding-history', [FarmerFeedingHistoryController::class, 'store']);
            Route::put('farmers/{farmer}/feeding-history/{feedingHistory}', [FarmerFeedingHistoryController::class, 'update']);
            Route::delete('farmers/{farmer}/feeding-history/{feedingHistory}', [FarmerFeedingHistoryController::class, 'destroy']);

            // Cows - accessible to all authenticated users
            Route::apiResource('cows', CowController::class);

            // Milk Deliveries - with policy checks
            Route::get('milk-deliveries/summary/daily', [MilkDeliveryController::class, 'dailySummary']);
            Route::apiResource('milk-deliveries', MilkDeliveryController::class);

            // Cow Milk Productions - accessible to all authenticated users
            Route::apiResource('cow-milk-productions', CowMilkProductionController::class);

            Route::apiResource('vets', \App\Http\Controllers\VetController::class);
            Route::apiResource('cow-treatments', \App\Http\Controllers\CowTreatmentController::class);
            Route::apiResource('partners', PartnerController::class);
            Route::get('partners/{partner}/invitations', [PartnerInvitationController::class, 'index']);
            Route::post('partners/{partner}/invitations', [PartnerInvitationController::class, 'store']);
            Route::delete('partners/{partner}/invitations/{invitation}', [PartnerInvitationController::class, 'destroy']);


        // User management - super_admin only
        Route::middleware('role:super_admin')->group(function () {
            Route::apiResource('users', \App\Http\Controllers\UserController::class);
            Route::post('users/{user}/toggle-active', [\App\Http\Controllers\UserController::class, 'toggleActive']);
        });

        // Agents - super_admin, admin, partners, and mcc can manage
        Route::middleware('role:super_admin|admin|partner|mcc')->group(function () {
            Route::apiResource('agents', \App\Http\Controllers\AgentController::class);
        });

});


});
