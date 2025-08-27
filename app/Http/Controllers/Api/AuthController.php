<?php

namespace App\Http\Controllers\Api;

use App\Events\PasswordReseted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateUserRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        // Récupérer le rôle via role_id envoyé dans $data
        $role = Role::findOrFail($data['role_id']);

        // Remplacer la valeur active_role (ex: id) par le nom du rôle
        $data['active_role'] = $role->name;

        // Retirer role_id du tableau pour éviter d'avoir un champ inconnu dans users
        unset($data['role_id']);


        $user = User::query()->create($data);

        // Associer le rôle dans la table pivot
        $user->roles()->attach($role->id);

        event(new Registered($user));

        return response()->json([
            'message' => 'User successfully registered, Check your mail to verify your account.'
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

    public function emailVerify($id, $hash, Request $request) {
        // Find user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Verify if the hash is correct
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        // Mark email as verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json(['message' => 'Email verified successfully!']);
    }

    public function ResetPasswordNotif(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        // return $status === Password::ResetLinkSent
        //     ? back()->with(['status' => __($status)])
        //     : back()->withErrors(['email' => __($status)]);

        return $status === Password::ResetLinkSent
            ? response()->json(['message' => __($status)])
            : response()->json(['email' => __($status)], 404);

        // $user = User::where('email', $request->email)->first();
        // if ($user) {
        //     $user->sendPasswordResetNotification($token);
        //     return response()->json(['message' => 'Password reset email sent successfully.']);
        // } else {
        //     return response()->json(['message' => 'Email not found.'], 404);
        // }
    }

    public function ResetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $status = Password::reset($request->only('email', 'password', 'token'), function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
            event(new PasswordReseted($user));
        });

        return $status === Password::PasswordReset
            ? response()->json(['message' => __($status)])
            : response()->json(['email' => __($status)], 404);
    }

    public function switchRole(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string', // on attend le nom du rôle à activer
        ]);

        $user = $request->user(); // utilisateur authentifié

        // Vérifier que l'utilisateur a ce rôle dans ses rôles liés
        $role = $user->roles()->where('name', $request->role_name)->first();

        if (!$role) {
            return response()->json([
                'message' => 'Le rôle spécifié n\'est pas associé à cet utilisateur.'
            ], 403);
        }

        // Mettre à jour le rôle actif
        $user->active_role = $role->name;
        $user->save();

        return response()->json([
            'message' => 'Rôle actif changé avec succès.',
            'active_role' => $user->active_role,
        ]);
    }

}
