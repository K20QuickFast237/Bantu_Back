<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Formation;
use App\Models\Experience;
use App\Models\Skill;
use App\Policies\FormationPolicy;
use App\Policies\ExperiencePolicy;
use App\Policies\SkillPolicy;
use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Formation::class => FormationPolicy::class,
        Experience::class => ExperiencePolicy::class,
        Skill::class => SkillPolicy::class,
        OffreEmploi::class => OffreEmploiPolicy::class,
        Candidature::class => CandidaturePolicy::class,
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

        Gate::define('isCandidat', fn($user) => $user->isCandidat());
        Gate::define('isRecruteur', fn($user) => $user->isRecruteur());
    }
}
