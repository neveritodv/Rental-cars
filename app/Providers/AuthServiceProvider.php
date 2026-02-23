<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
         Vehicle::class => VehiclePolicy::class,
        VehicleModel::class => VehicleModelPolicy::class,
         VehicleVignette::class => VehicleVignettePolicy::class,
          RentalContract::class => RentalContractPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}