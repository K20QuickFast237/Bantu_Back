<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Formation;
use App\Models\Experience;
use App\Policies\FormationPolicy;
use App\Policies\ExperiencePolicy;


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
