<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-google-login', function () {
    return view('test-google-login-labs');
});


Route::get('/auth/redirect', function ()
{
    return Socialite::driver('google')
        ->scopes(['https://www.googleapis.com/auth/calendar'])
        ->with([
            'prompt' => 'consent',
        ])
        ->redirect();
});