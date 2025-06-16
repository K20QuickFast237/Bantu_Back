<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
// use Illuminate\Http\Request;                         // 1
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {    // 1
//     return $request->user();
// })->middleware('auth:api');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

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
