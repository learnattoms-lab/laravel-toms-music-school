<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OAuthCredential;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\OAuthServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

/**
 * OAuth Service
 *
 * Handles OAuth authentication flows for Google, Apple, and Facebook.
 * Also manages OAuth credentials for Google Calendar integration.
 *
 * Note: Requires laravel/socialite package
 * Install via: composer require laravel/socialite
 */
class OAuthService implements OAuthServiceInterface
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get the OAuth redirect URL for Google authentication.
     */
    public function getGoogleRedirectUrl(): string
    {
        try {
            return Socialite::driver('google')
                ->scopes(['openid', 'email', 'profile'])
                ->redirect()
                ->getTargetUrl();
        } catch (\Exception $e) {
            Log::error('Failed to generate Google OAuth redirect URL', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to generate OAuth redirect URL: ' . $e->getMessage());
        }
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback(string $code): User
    {
        try {
            // Exchange code for token
            $tokenData = $this->exchangeCodeForToken($code);

            if (!isset($tokenData['access_token'])) {
                throw new \Exception('Failed to get access token from Google');
            }

            // Get user info from Google
            $userInfo = $this->getGoogleUserInfo($tokenData['access_token']);

            // Find or create user
            $user = $this->findOrCreateUser($userInfo, 'google');

            // Store OAuth credentials for Google Calendar integration
            if (isset($tokenData['refresh_token'])) {
                $this->storeCredentials($user, $tokenData, 'google');
            }

            // Update last login
            $this->userRepository->update($user, [
                'last_login_at' => now(),
            ]);

            Log::info('Google OAuth callback successful', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Google OAuth callback failed', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('OAuth callback failed: ' . $e->getMessage());
        }
    }

    /**
     * Exchange authorization code for access token (for Google Calendar).
     */
    public function exchangeCodeForToken(string $code): array
    {
        try {
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            $redirectUri = config('services.google.redirect_uri');

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if (!$response->successful()) {
                throw new \Exception('Token exchange failed with HTTP code: ' . $response->status());
            }

            $tokenData = $response->json();

            if (!$tokenData || !isset($tokenData['access_token'])) {
                throw new \Exception('Failed to parse token response');
            }

            Log::info('OAuth token exchange successful');

            return $tokenData;
        } catch (\Exception $e) {
            Log::error('OAuth token exchange failed', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Token exchange failed: ' . $e->getMessage());
        }
    }

    /**
     * Get Google user information.
     */
    public function getGoogleUserInfo(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if (!$response->successful()) {
                throw new \Exception('Failed to get user info with HTTP code: ' . $response->status());
            }

            $userInfo = $response->json();

            if (!$userInfo) {
                throw new \Exception('Failed to parse user info response');
            }

            return $userInfo;
        } catch (\Exception $e) {
            Log::error('Failed to get Google user info', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to get user info: ' . $e->getMessage());
        }
    }

    /**
     * Find or create a user from OAuth data.
     */
    public function findOrCreateUser(array $userInfo, string $provider = 'google'): User
    {
        try {
            $email = $userInfo['email'] ?? null;

            if (!$email) {
                throw new \Exception('Email not provided in OAuth user info');
            }

            // Check if user already exists
            $user = $this->userRepository->findByEmail($email);

            if ($user) {
                // Update existing user's OAuth ID if not set
                $updateData = [];
                $googleId = $userInfo['id'] ?? null;

                if ($provider === 'google' && $googleId && !$user->google_id) {
                    $updateData['google_id'] = $googleId;
                }

                if (!$user->email_verified) {
                    $updateData['email_verified'] = true;
                }

                if (isset($userInfo['picture']) && !$user->profile_picture) {
                    $updateData['profile_picture'] = $userInfo['picture'];
                }

                if (!empty($updateData)) {
                    $this->userRepository->update($user, $updateData);
                }

                return $user;
            }

            // Create new user
            $userData = [
                'email' => $email,
                'first_name' => $userInfo['given_name'] ?? '',
                'last_name' => $userInfo['family_name'] ?? '',
                'email_verified' => true,
                'is_active' => true,
                'roles' => ['ROLE_USER'],
            ];

            if ($provider === 'google' && isset($userInfo['id'])) {
                $userData['google_id'] = $userInfo['id'];
            }

            if (isset($userInfo['picture'])) {
                $userData['profile_picture'] = $userInfo['picture'];
            }

            // Generate a random password (users can reset it if needed)
            $userData['password'] = Hash::make(bin2hex(random_bytes(32)));

            $user = $this->userRepository->create($userData);

            Log::info('New user created via OAuth', [
                'user_id' => $user->id,
                'email' => $user->email,
                'provider' => $provider,
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to find or create user from OAuth', [
                'error' => $e->getMessage(),
                'provider' => $provider,
            ]);
            throw new \Exception('Failed to find or create user: ' . $e->getMessage());
        }
    }

    /**
     * Store OAuth credentials for a user (for Google Calendar integration).
     */
    public function storeCredentials(User $user, array $tokenData, string $provider = 'google'): OAuthCredential
    {
        try {
            // Check if credentials already exist
            $existingCredential = OAuthCredential::where('user_id', $user->id)
                ->where('provider', $provider)
                ->first();

            $expiresAt = null;
            if (isset($tokenData['expires_in'])) {
                $expiresAt = now()->addSeconds($tokenData['expires_in']);
            }

            $credentialData = [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null,
                'expires_at' => $expiresAt,
            ];

            if ($existingCredential) {
                // Update existing credentials
                $existingCredential->update($credentialData);
                Log::info('OAuth credentials updated', [
                    'user_id' => $user->id,
                    'provider' => $provider,
                ]);
                return $existingCredential;
            }

            // Create new credentials
            $credentialData['user_id'] = $user->id;
            $credentialData['provider'] = $provider;

            $credential = OAuthCredential::create($credentialData);

            Log::info('OAuth credentials stored', [
                'user_id' => $user->id,
                'provider' => $provider,
            ]);

            return $credential;
        } catch (\Exception $e) {
            Log::error('Failed to store OAuth credentials', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'provider' => $provider,
            ]);
            throw new \Exception('Failed to store credentials: ' . $e->getMessage());
        }
    }

    /**
     * Refresh OAuth access token.
     */
    public function refreshToken(OAuthCredential $credential): OAuthCredential
    {
        try {
            if (!$credential->refresh_token) {
                throw new \Exception('No refresh token available');
            }

            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'refresh_token' => $credential->refresh_token,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'refresh_token',
            ]);

            if (!$response->successful()) {
                throw new \Exception('Token refresh failed with HTTP code: ' . $response->status());
            }

            $tokenData = $response->json();

            if (!$tokenData || !isset($tokenData['access_token'])) {
                throw new \Exception('Failed to parse token response');
            }

            // Update credential
            $updateData = [
                'access_token' => $tokenData['access_token'],
            ];

            if (isset($tokenData['expires_in'])) {
                $updateData['expires_at'] = now()->addSeconds($tokenData['expires_in']);
            }

            $credential->update($updateData);

            Log::info('OAuth token refreshed', [
                'user_id' => $credential->user_id,
                'provider' => $credential->provider,
            ]);

            return $credential->fresh();
        } catch (\Exception $e) {
            Log::error('Failed to refresh OAuth token', [
                'error' => $e->getMessage(),
                'credential_id' => $credential->id,
            ]);
            throw new \Exception('Token refresh failed: ' . $e->getMessage());
        }
    }
}

