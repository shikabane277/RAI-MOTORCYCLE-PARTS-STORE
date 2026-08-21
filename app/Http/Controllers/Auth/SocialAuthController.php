<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CartController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected array $allowedProviders = ['google'];

    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            return redirect()->route('login')->with('error', 'Unsupported login provider.');
        }

        // If credentials are not set in .env yet, provide a mock/demo login in local development
        if (!config("services.{$provider}.client_id")) {
            return $this->handleDemoSocialLogin($provider);
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', "Could not connect to {$provider}. " . $e->getMessage());
        }
    }

    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            return redirect()->route('login')->with('error', 'Unsupported login provider.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$user) {
                // Check if account exists with same email
                $user = User::where('email', $socialUser->getEmail())->first();

                if ($user) {
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                    ]);
                } else {
                    $user = User::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? ucfirst($provider) . ' User',
                        'email' => $socialUser->getEmail() ?? ($socialUser->getId() . "@{$provider}.raimotorcycleparts.ph"),
                        'password' => Hash::make(Str::random(24)),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                        'role' => 'customer',
                    ]);
                }
            }

            Auth::login($user, true);

            if ($redirect = CartController::processPendingCartAction($user)) {
                return $redirect;
            }

            return redirect()->intended(route('home'))->with('success', 'Logged in successfully with ' . ucfirst($provider) . '!');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to authenticate via ' . ucfirst($provider) . ': ' . $e->getMessage());
        }
    }

    /**
     * Demo / Fallback social login when API credentials are not yet configured in .env
     */
    protected function handleDemoSocialLogin(string $provider)
    {
        $demoEmail = "demo.{$provider}@raimotorcycleparts.ph";
        $providerName = ucfirst($provider);

        $user = User::where('email', $demoEmail)->first();

        if (!$user) {
            $user = User::create([
                'name' => "Rider ({$providerName} Demo)",
                'email' => $demoEmail,
                'password' => Hash::make('password'),
                'provider' => $provider,
                'provider_id' => 'demo_' . $provider . '_12345',
                'role' => 'customer',
                'loyalty_points' => 150,
            ]);
        }

        Auth::login($user, true);

        if ($redirect = CartController::processPendingCartAction($user)) {
            return $redirect;
        }

        return redirect()->route('home')->with('success', "Logged in with {$providerName} (Demo Account).");
    }
}
