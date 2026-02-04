<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay;

use Tigusigalpa\Ebay\Enums\Site;
use Tigusigalpa\Ebay\Exceptions\InvalidConfigurationException;
use Tigusigalpa\Ebay\Http\Auth;
use Tigusigalpa\Ebay\Http\Clients\CommerceClient;
use Tigusigalpa\Ebay\Http\Clients\TradingClient;

/**
 * Main eBay API Client
 * 
 * This library is authored by Igor Sazonov <sovletig@gmail.com>
 * Modern PHP/Laravel package for eBay API integration
 * 
 * @link https://github.com/tigusigalpa/ebay-php
 * @link https://developer.ebay.com/
 */
class Ebay
{
    protected Auth $auth;
    protected TradingClient $trading;
    protected CommerceClient $commerce;
    protected Site $site;
    protected string $environment;

    public function __construct(?string $environment = null, ?Site $site = null)
    {
        $this->environment = $environment ?? config('ebay.environment', 'sandbox');
        $this->site = $site ?? $this->getDefaultSite();
        
        $this->validateConfiguration();
        $this->initializeClients();
    }

    protected function validateConfiguration(): void
    {
        $required = ['app_id', 'cert_id', 'dev_id', 'runame'];
        
        foreach ($required as $key) {
            if (empty(config("ebay.{$this->environment}.{$key}"))) {
                throw new InvalidConfigurationException(
                    "Missing required configuration: ebay.{$this->environment}.{$key}"
                );
            }
        }
    }

    protected function initializeClients(): void
    {
        $this->auth = new Auth(
            $this->environment,
            config("ebay.{$this->environment}.app_id"),
            config("ebay.{$this->environment}.cert_id"),
            config("ebay.{$this->environment}.runame")
        );

        $this->trading = new TradingClient(
            $this->environment,
            config("ebay.{$this->environment}.app_id"),
            config("ebay.{$this->environment}.cert_id"),
            config("ebay.{$this->environment}.dev_id"),
            config("ebay.{$this->environment}.runame"),
            $this->site,
            $this->auth
        );

        $this->commerce = new CommerceClient(
            $this->environment,
            config("ebay.{$this->environment}.app_id"),
            config("ebay.{$this->environment}.cert_id"),
            config("ebay.{$this->environment}.dev_id"),
            config("ebay.{$this->environment}.runame"),
            $this->site,
            $this->auth
        );
    }

    protected function getDefaultSite(): Site
    {
        $defaultSiteCode = config('ebay.default_site', 'US');
        $site = Site::fromCode($defaultSiteCode);
        
        if (!$site) {
            return Site::US;
        }
        
        return $site;
    }

    public function auth(): Auth
    {
        return $this->auth;
    }

    public function trading(): TradingClient
    {
        return $this->trading;
    }

    public function commerce(): CommerceClient
    {
        return $this->commerce;
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;
        $this->initializeClients();
        return $this;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function setEnvironment(string $environment): self
    {
        if (!in_array($environment, ['sandbox', 'production'])) {
            throw new InvalidConfigurationException(
                "Invalid environment: {$environment}. Must be 'sandbox' or 'production'"
            );
        }
        
        $this->environment = $environment;
        $this->validateConfiguration();
        $this->initializeClients();
        return $this;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function setAccessToken(string $token, int $expiresAt): self
    {
        $this->trading->setAccessToken($token, $expiresAt);
        $this->commerce->setAccessToken($token, $expiresAt);
        return $this;
    }

    public function setRefreshToken(string $token, int $expiresAt): self
    {
        $this->trading->setRefreshToken($token, $expiresAt);
        $this->commerce->setRefreshToken($token, $expiresAt);
        return $this;
    }

    public function getConsentUrl(
        ?array $scopes = null,
        ?string $state = null,
        ?string $locale = null
    ): string {
        return $this->auth->getConsentUrl(
            $scopes ?? config('ebay.scopes', []),
            $state,
            $locale ?? $this->site->locale()
        );
    }

    public function exchangeCodeForToken(string $code): array
    {
        $tokenData = $this->auth->getAccessTokenByCode($code);
        
        $this->setAccessToken($tokenData['access_token'], $tokenData['expires_at']);
        
        if ($tokenData['refresh_token']) {
            $this->setRefreshToken(
                $tokenData['refresh_token'],
                $tokenData['refresh_token_expires_at']
            );
        }
        
        return $tokenData;
    }

    public static function getUserUrl(Site $site, string $username): string
    {
        return rtrim($site->url(), '/') . '/usr/' . $username;
    }
}
