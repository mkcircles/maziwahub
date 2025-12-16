<?php

namespace App\Providers;

use App\Models\Farmer;
use App\Models\MilkCollectionCenter;
use App\Models\MilkDelivery;
use App\Models\User;
use App\Policies\FarmerPolicy;
use App\Policies\MilkCollectionCenterPolicy;
use App\Policies\MilkDeliveryPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Farmer::class => FarmerPolicy::class,
        MilkCollectionCenter::class => MilkCollectionCenterPolicy::class,
        MilkDelivery::class => MilkDeliveryPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
