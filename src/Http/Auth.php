<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tigusigalpa\Ebay\Exceptions\AuthenticationException;

/**
 * OAuth 2.0 Authentication Handler
 * 
 * @link https://developer.ebay.com/api-docs/static/oauth-consent-request.html
 * @link https://developer.ebay.com/api-docs/static/oauth-client-credentials-grant.html
 * @link https://developer.ebay.com/api-docs/static/oauth-auth-code-grant-request.html
 * @link https://developer.ebay.com/api-docs/static/oauth-refresh-token-request.html
 */
class Auth
{
    protected const TOKEN_ENDPOINTS = [
        'sandbox' => 'https://api.sandbox.ebay.com/identity/v1/oauth2/token',
        'production' => 'https://api.ebay.com/identity/v1/oauth2/token',
    ];

    protected const AUTH_ENDPOINTS = [
        'sandbox' => 'https://auth.sandbox.ebay.com/oauth2/authorize',
        'production' => 'https://auth.ebay.com/oauth2/authorize',
    ];

    public function __construct(
        protected string $environment,
        protected string $appId,
        protected string $certId,
        protected string $ruName
    ) {
    }

    /**
     * Generate OAuth consent URL for user authorization
     * 
     * @link https://developer.ebay.com/api-docs/static/oauth-consent-request.html
     */
    public function getConsentUrl(array $scopes, ?string $state = null, string $locale = 'en-US'): string
    {
        $params = [
            'client_id' => $this->appId,
            'redirect_uri' => $this->ruName,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'locale' => $locale,
            'prompt' => 'login',
        ];

        if ($state !== null) {
            $params['state'] = $state;
        }

        return self::AUTH_ENDPOINTS[$this->environment] . '?' . Arr::query($params);
    }

    /**
     * Exchange authorization code for access token
     * 
     * @link https://developer.ebay.com/api-docs/static/oauth-auth-code-grant-request.html
     */
    public function getAccessTokenByCode(string $code): array
    {
        $response = Http::asForm()
            ->withBasicAuth($this->appId, $this->certId)
            ->post(self::TOKEN_ENDPOINTS[$this->environment], [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->ruName,
            ]);

        return $this->handleTokenResponse($response);
    }

    /**
     * Get application access token using client credentials grant
     * 
     * @link https://developer.ebay.com/api-docs/static/oauth-client-credentials-grant.html
     */
    public function getApplicationToken(array $scopes = []): array
    {
        $cacheKey = 'ebay_app_token_' . md5($this->appId . implode(',', $scopes));

        return Cache::remember($cacheKey, now()->addMinutes(50), function () use ($scopes) {
            $response = Http::asForm()
                ->withBasicAuth($this->appId, $this->certId)
                ->post(self::TOKEN_ENDPOINTS[$this->environment], [
                    'grant_type' => 'client_credentials',
                    'scope' => implode(' ', $scopes ?: config('ebay.scopes', [])),
                ]);

            return $this->handleTokenResponse($response);
        });
    }

    /**
     * Refresh an expired access token
     * 
     * @link https://developer.ebay.com/api-docs/static/oauth-refresh-token-request.html
     */
    public function refreshAccessToken(string $refreshToken, array $scopes = []): array
    {
        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        if (!empty($scopes)) {
            $data['scope'] = implode(' ', $scopes);
        }

        $response = Http::asForm()
            ->withBasicAuth($this->appId, $this->certId)
            ->post(self::TOKEN_ENDPOINTS[$this->environment], $data);

        return $this->handleTokenResponse($response);
    }

    /**
     * Handle OAuth token response
     */
    protected function handleTokenResponse(Response $response): array
    {
        if ($response->failed()) {
            $error = $response->json();
            throw new AuthenticationException(
                $error['error_description'] ?? 'Authentication failed',
                $error['error'] ?? 'unknown_error',
                $response->body()
            );
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'],
            'token_type' => $data['token_type'] ?? 'Bearer',
            'expires_in' => $data['expires_in'],
            'expires_at' => now()->addSeconds($data['expires_in'])->timestamp,
            'refresh_token' => $data['refresh_token'] ?? null,
            'refresh_token_expires_in' => $data['refresh_token_expires_in'] ?? null,
            'refresh_token_expires_at' => isset($data['refresh_token_expires_in']) 
                ? now()->addSeconds($data['refresh_token_expires_in'])->timestamp 
                : null,
        ];
    }

    /**
     * Check if access token is expired
     */
    public function isTokenExpired(int $expiresAt): bool
    {
        return now()->timestamp >= $expiresAt;
    }

    /**
     * Check if refresh token is expired
     */
    public function isRefreshTokenExpired(int $expiresAt): bool
    {
        return now()->timestamp >= $expiresAt;
    }
}
