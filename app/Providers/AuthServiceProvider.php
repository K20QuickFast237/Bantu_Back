<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Formation;
use App\Models\Experience;
use App\Policies\FormationPolicy;
use App\Policies\ExperiencePolicy;
use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Formation::class => FormationPolicy::class,
        Experience::class => ExperiencePolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
