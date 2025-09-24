<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
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

        // Custom Verification Email message
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            $code = substr($url, strpos($url, '?') - 11, 6);
            $link = substr($url, strpos($url, 'verify/') + 7);
            $url = env('FRONT_URL').'/email-verify?token='.str_replace('?', '&', $link);

            return (new MailMessage)
                ->subject('Verify Email Address')
                ->greeting('Hello '.$notifiable->nom.' '.$notifiable->prenom.',')
                ->line("Please enter the code <b>$code</b> to verify your email address.")
                ->line('Or click the button below.')
                ->action('Verify Email Address', $url.'&code='.$code)
                ->line('If you did not create an account, no further action is required.')
                ->line('Thank you for using our application!');
        });
    }
}
