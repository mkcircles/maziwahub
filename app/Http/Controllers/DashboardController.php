<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MilkCollectionCenter;
use App\Models\Cow;
use App\Models\Farmer;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get summary statistics for the admin dashboard.
     */
    public function adminSummary(): JsonResponse
    {
        $activeUsers = User::where('is_active', true)->count();
        $registeredFarmers = Farmer::count();
        $milkCenters = MilkCollectionCenter::count();
        $cowsMonitored = Cow::count();

        return response()->json([
            'active_users' => $activeUsers,
            'registered_farmers' => $registeredFarmers,
            'milk_centers' => $milkCenters,
            'cows_monitored' => $cowsMonitored,
        ]);
    }
}
