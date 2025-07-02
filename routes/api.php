<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ParticulierProfileController;
use App\Http\Controllers\Api\ProfessionnelProfileController;
use App\Http\Controllers\Profil\FormationController;
use App\Http\Controllers\Profil\ExperienceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;


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
Route::post('forgot-password', [AuthController::class, 'ResetPasswordNotif'])->middleware('guest')->name('password.email');;
Route::get('/reset-password/{token}', function (string $token) {
    // return response()->json(['token' => $token]);
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'ResetPassword'])->middleware('guest')->name('password.update');

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

    Route::middleware('auth:api')->group(function () {
        Route::post('/profile/particulier', [ParticulierProfileController::class, 'store']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/profile/professionnel', [ProfessionnelProfileController::class, 'store']);
    });

    Route::middleware('auth:api')->group(function () {
        // Formations
        Route::apiResource('formations', FormationController::class)->only(['index', 'store', 'update', 'destroy']);

        // Expériences
        Route::apiResource('experiences', ExperienceController::class)->only(['index', 'store', 'update', 'destroy']);
});
});
