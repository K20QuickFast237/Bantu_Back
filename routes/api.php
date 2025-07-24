<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api')->middleware('verified'); // Verified to ensure the user's email is verified

// Route to show the email verification notice
Route::get('/email/verify', function () {
    return response()->json(['message' => 'Check your mail to verify your account.']);
})->name('verification.notice');

// Route to verify the email (email verification handler)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'emailVerify'])->middleware('signed')->name('verification.verify');

// Route to resend the verification email
Route::post('/email/verification-notification', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.'], 400);
    }

    $user->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification email has been sent again. Check your mail to verify your account.']);
})->middleware('throttle:6,1')->name('verification.send');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'ResetPasswordNotif'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', function (string $token) {
    // return response()->json(['token' => $token]);
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'ResetPassword'])->middleware('guest')->name('password.update');

//Login With Goolgle
Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->stateless()->redirect();
    // ->scopes([''])
    // ->with(['hd' => 'example.com'])
});
Route::post('/google-login', function (Request $request) {
    $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
    $payload = $client->verifyIdToken($request->JWT_ID_Token);
    return response()->json($payload, 200);
});
Route::get('/google-login-callback', function (Request $request) {
    file_put_contents('test.txt', date('d-m-Y') . ': \n' . json_encode($request->all()));
    return response()->json($request->all(), 200);

    $googleUser = Socialite::driver('google')->stateless()->user();

    $googleUser = User::updateOrCreate([
        'google_id' => $googleUser->id,
    ], [
        'name' => $googleUser->name,
        'email' => $googleUser->email,
        'github_token' => $googleUser->token,
        'github_refresh_token' => $googleUser->refreshToken,
    ]);

    // Auth::login($googleUser);
    $token = $googleUser->createToken('authToken')->accessToken;
    return response()->json([
        'token' => $token
    ], 200);

    // return redirect('/dashboard');
});
//Login with LinkedIn
Route::get('/linkedin-login', [AuthController::class, 'linkedinLogin'])->middleware('guest');
Route::get('/linkedin-login-callback', [AuthController::class, 'handlelinkedinCallback']);


Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('a_products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('save', [ProductController::class, 'save']);
        Route::get('{id}/show', [ProductController::class, 'show']);
        Route::patch('{id}/update', [ProductController::class, 'update']);
        Route::delete('{id}/delete', [ProductController::class, 'delete']);
    });
});
