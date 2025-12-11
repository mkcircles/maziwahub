<?php

namespace App\Providers;

use App\Models\Agent;
use App\Models\Farmer;
use App\Models\MilkCollectionCenter;
use App\Models\MilkDelivery;
use App\Models\Partner;
use App\Models\User;
use App\Policies\AgentPolicy;
use App\Policies\FarmerPolicy;
use App\Policies\MilkCollectionCenterPolicy;
use App\Policies\MilkDeliveryPolicy;
use App\Policies\PartnerPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Agent::class => AgentPolicy::class,
        Farmer::class => FarmerPolicy::class,
        MilkCollectionCenter::class => MilkCollectionCenterPolicy::class,
        MilkDelivery::class => MilkDeliveryPolicy::class,
        Partner::class => PartnerPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (User $user) {
            return $user->isSuperAdmin() ? true : null;
        });
    }
}
