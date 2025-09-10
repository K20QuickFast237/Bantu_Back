<?php

namespace App\Http\Controllers\Api;

use App\Events\PasswordReseted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateUserRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(CreateUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
        $user = User::query()->create($data);
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
            return $this->LogUserIn($user);
        }

        return response()->json([
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    Private function LogUserIn(User $user) {
        User::query()->where('id', $user->id)->update([
            'last_login' => now()
        ]);
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user)
        ], 200);
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

    public function emailVerify($id, $hash, Request $request)
    {
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

    public function ResetPasswordNotif(Request $request)
    {
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

    public function ResetPassword(Request $request)
    {
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

    public function googleLogin(Request $request) 
    {
        $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $userData = $client->verifyIdToken($request->JWT_ID_Token);

        if (!$userData) {
            return response()->json(['message' => 'Invalid or expired token.'], 401);
        }

        $user = $this->findOrCreateUser($userData);
        return $this->LogUserIn($user);
    }

    public function linkedinLogin()
    {
        $clientId = env('LINKEDIN_CLIENT_ID');
        $redirectUri = env('LINKEDIN_REDIRECT');
        $scope = 'openid%20profile%20email';
        $response_type = 'code';

        // $state = bin2hex(random_bytes(16));
        // session(['linkedin_state' => $state]); //optional
        // if you are creating state then you also need to pass in url


        return redirect("https://www.linkedin.com/oauth/v2/authorization?response_type=$response_type&client_id=$clientId&redirect_uri=$redirectUri&scope=$scope");
    }

    public function handlelinkedinCallback(Request $request)
    {
        // $state = $request->query('state');
        //  Verify the state parameter to prevent CSRF attacks
        // if (!$state || $state !== $request->session()->pull('linkedin_state')) {
        //     return response()->json(['error' => 'Invalid state'], 400);
        // }

        $code = $request->query('code');
        if (empty($code)) {
            return response()->json(['error' => 'Authorization code not provided'], 400);
        }

        $clientId = env('LINKEDIN_CLIENT_ID'); //config('services.linkedin.client_id');
        $clientSecret = env('LINKEDIN_CLIENT_SECRET'); //config('services.linkedin.client_secret');
        $redirectUri = env('LINKEDIN_REDIRECT'); //config('services.linkedin.redirect_uri');
        
        $response = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
            'code'          => $code,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code',
        ]);
        $accessToken = $response->json('access_token');
        $userData = $this->getUserData($accessToken);
        $user = $this->findOrCreateUser($userData);
        Auth::login($user);

        return $this->LogUserIn($user);
    }

    private function getUserData($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get('https://api.linkedin.com/v2/userinfo');
        return $response->json();
    }

    private function findOrCreateUser(array $userData)
    {
        $user = User::where('email', $userData['email'])->first();
        if (!$user) {
            // Create a new user if not exists in database
            $user = User::create([
                'nom' => $userData['family_name'],
                'prenom' => $userData['given_name'],
                'email' => $userData['email'],
                'email_verified_at' => $userData['email_verified'] ? now() : null,
                'password' => $userData['sub'],
                'photo_profil' => $userData['picture'],
            ]);
            $user = User::where('email', $user['email'])->first();  //->where('linkedin_id', $user['linkedin_id'])
        }
        return $user;
    }
}
