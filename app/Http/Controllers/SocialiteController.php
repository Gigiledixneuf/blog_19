<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    protected $allowedProviders = ['google'];

    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            return response()->json(['error' => 'Provider non supporté'], 400);
        }

        // Redirection avec option pour forcer le choix du compte
        return Socialite::driver($provider)
            ->stateless()
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function callback(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            return response()->json(['error' => 'Provider non supporté'], 400);
        }

        try {
            $oauthUser = Socialite::driver($provider)->stateless()->user();

            $user = User::updateOrCreate(
                [
                    'provider' => $provider,
                    'provider_id' => $oauthUser->getId(),
                ],
                [
                    'email' => $oauthUser->getEmail(),
                    'name' => $oauthUser->getName() ?? $oauthUser->getNickname(),
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur OAuth',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
