<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command ("auth:clear-resets")->purpose("Clear expired password resets")->daily();
Schedule::command("passport:purge")->purpose('Purge expired tokens')->daily();

/*
    Il ne reste plus qu'a confiurer sur le serveur un cron qui lancera la commande
    php artisan schedule:run
    à une fréquence suffisante pour satisfaire à l'exécution des différentes tâches.
*/