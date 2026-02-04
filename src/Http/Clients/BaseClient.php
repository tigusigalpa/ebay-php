<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Http\Clients;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tigusigalpa\Ebay\Enums\Site;
use Tigusigalpa\Ebay\Exceptions\EbayApiException;
use Tigusigalpa\Ebay\Http\Auth;

abstract class BaseClient
{
    protected ?string $accessToken = null;
    protected ?int $accessTokenExpiresAt = null;
    protected ?string $refreshToken = null;
    protected ?int $refreshTokenExpiresAt = null;

    public function __construct(
        protected string $environment,
        protected string $appId,
        protected string $certId,
        protected string $devId,
        protected string $ruName,
        protected Site $site,
        protected Auth $auth
    ) {
    }

    public function setAccessToken(string $token, int $expiresAt): self
    {
        $this->accessToken = $token;
        $this->accessTokenExpiresAt = $expiresAt;
        return $this;
    }

    public function setRefreshToken(string $token, int $expiresAt): self
    {
        $this->refreshToken = $token;
        $this->refreshTokenExpiresAt = $expiresAt;
        return $this;
    }

    public function getAccessToken(): ?string
    {
        if ($this->accessToken && $this->accessTokenExpiresAt) {
            if ($this->auth->isTokenExpired($this->accessTokenExpiresAt)) {
                if ($this->refreshToken && !$this->auth->isRefreshTokenExpired($this->refreshTokenExpiresAt)) {
                    $tokenData = $this->auth->refreshAccessToken($this->refreshToken);
                    $this->setAccessToken($tokenData['access_token'], $tokenData['expires_at']);
                    if ($tokenData['refresh_token']) {
                        $this->setRefreshToken($tokenData['refresh_token'], $tokenData['refresh_token_expires_at']);
                    }
                    return $this->accessToken;
                }
                return null;
            }
            return $this->accessToken;
        }
        return null;
    }

    protected function log(string $message, array $context = []): void
    {
        if (config('ebay.logging.enabled', false)) {
            Log::channel(config('ebay.logging.channel', 'stack'))
                ->info($message, $context);
        }
    }

    protected function handleError(string $message, array $errors = [], ?string $response = null): void
    {
        throw new EbayApiException(
            $message,
            $errors[0]['code'] ?? 'unknown',
            $response,
            $errors
        );
    }
}
