<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Formation;
use App\Models\Experience;
use App\Models\OffreEmploi;
use App\Models\Candidature;
use App\Models\Skill;
use App\Models\Shop;
use App\Policies\FormationPolicy;
use App\Policies\ExperiencePolicy;
use App\Policies\SkillPolicy;
use App\Policies\OffreEmploiPolicy;
use App\Policies\CandidaturePolicy;
use App\Models\Produit;
use App\Models\Panier;
use App\Models\Favori;
use App\Models\Commande;
use App\Models\Paiement;
use App\Policies\ProductPolicy;
use App\Policies\PanierPolicy;
use App\Policies\FavoriPolicy;
use App\Policies\CommandePolicy;
use App\Policies\PaiementPolicy;
use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Formation::class => FormationPolicy::class,
        Experience::class => ExperiencePolicy::class,
        Skill::class => SkillPolicy::class,
        OffreEmploi::class => OffreEmploiPolicy::class,
        Candidature::class => CandidaturePolicy::class,

        Produit::class => ProductPolicy::class,
        Panier::class => PanierPolicy::class,
        Favori::class => FavoriPolicy::class,
        Commande::class => CommandePolicy::class,
        Paiement::class => PaiementPolicy::class,
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
        Gate::define('isVendeur', fn($user) => $user->isVendeur());
        Gate::define('isAdmin', fn($user) => $user->isAdmin());

        Gate::define('update-shop', function($user, Shop $shop) {
    // Le vendeur ne peut modifier que ses propres boutiques
    return $user->isVendeur() && $shop->user_id === $user->id;
});

    }
}
