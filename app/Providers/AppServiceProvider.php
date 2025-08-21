<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
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
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
        Schema::defaultStringLength(200);
        // Passport::ignoreRoutes();
        // Passport::routes();
        // Passport::tokensExpireIn(now()->addDays(15));
        // Passport::refreshTokensExpireIn(now()->addDays(30));
        // Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // Personnalisation du lien de réinitialisation du mot de passe envoyé par mail.
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return env('FRONT_URL').'/reset-password?token='.$token.'&email='.$user->email;
        });
    }
}
