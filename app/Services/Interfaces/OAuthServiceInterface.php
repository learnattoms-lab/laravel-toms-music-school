<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\OAuthCredential;
use App\Models\User;

/**
 * OAuth Service Interface
 *
 * Defines the contract for OAuth authentication operations.
 */
interface OAuthServiceInterface
{
    /**
     * Get the OAuth redirect URL for Google authentication.
     *
     * @return string The redirect URL
     */
    public function getGoogleRedirectUrl(): string;

    /**
     * Handle Google OAuth callback.
     *
     * @param string $code The authorization code from Google
     * @return User The authenticated user
     */
    public function handleGoogleCallback(string $code): User;

    /**
     * Exchange authorization code for access token (for Google Calendar).
     *
     * @param string $code The authorization code
     * @return array Token data (access_token, refresh_token, expires_in, etc.)
     */
    public function exchangeCodeForToken(string $code): array;

    /**
     * Get Google user information.
     *
     * @param string $accessToken The access token
     * @return array User information from Google
     */
    public function getGoogleUserInfo(string $accessToken): array;

    /**
     * Find or create a user from OAuth data.
     *
     * @param array $userInfo User information from OAuth provider
     * @param string $provider OAuth provider (google, apple, facebook)
     * @return User The user model
     */
    public function findOrCreateUser(array $userInfo, string $provider = 'google'): User;

    /**
     * Store OAuth credentials for a user (for Google Calendar integration).
     *
     * @param User $user The user
     * @param array $tokenData Token data from OAuth exchange
     * @param string $provider OAuth provider (default: google)
     * @return OAuthCredential The stored credential
     */
    public function storeCredentials(User $user, array $tokenData, string $provider = 'google'): OAuthCredential;

    /**
     * Refresh OAuth access token.
     *
     * @param OAuthCredential $credential The OAuth credential
     * @return OAuthCredential Updated credential with new token
     */
    public function refreshToken(OAuthCredential $credential): OAuthCredential;
}

