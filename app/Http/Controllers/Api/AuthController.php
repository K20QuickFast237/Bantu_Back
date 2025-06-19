<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateUserRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
        User::query()->create($data);

        return response()->json([
            'message' => 'User registered successfully'
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            $user = Auth::user();
            User::query()->where('id', $user->id)->update([
                'last_login' => now()
            ]);
            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'token' => $token
            ], 200);
        }

        return response()->json([
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    public function user(): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Logged out successfully.'
        ], 200);
    }
}
