<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\Region;
use App\Models\District;
use App\Models\County;
use App\Models\Subcounty;
use App\Models\Parish;
use App\Models\Village;
use App\Models\MilkCollectionCenter;
use App\Models\Cow;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get summary statistics for the admin dashboard.
     */
    public function adminSummary(): JsonResponse
    {
        $activeUsers = User::where('is_active', true)->count();

        $geographyLayers = Country::count()
            + Region::count()
            + District::count()
            + County::count()
            + Subcounty::count()
            + Parish::count()
            + Village::count();

        $milkCenters = MilkCollectionCenter::count();
        $cowsMonitored = Cow::count();

        return response()->json([
            'active_users' => $activeUsers,
            'geography_layers' => $geographyLayers,
            'milk_centers' => $milkCenters,
            'cows_monitored' => $cowsMonitored,
        ]);
    }
}
