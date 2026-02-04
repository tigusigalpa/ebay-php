<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Facades;

use Illuminate\Support\Facades\Facade;
use Tigusigalpa\Ebay\Enums\Site;
use Tigusigalpa\Ebay\Http\Auth;
use Tigusigalpa\Ebay\Http\Clients\CommerceClient;
use Tigusigalpa\Ebay\Http\Clients\TradingClient;

/**
 * @method static Auth auth()
 * @method static TradingClient trading()
 * @method static CommerceClient commerce()
 * @method static \Tigusigalpa\Ebay\Ebay setSite(Site $site)
 * @method static Site getSite()
 * @method static \Tigusigalpa\Ebay\Ebay setEnvironment(string $environment)
 * @method static string getEnvironment()
 * @method static \Tigusigalpa\Ebay\Ebay setAccessToken(string $token, int $expiresAt)
 * @method static \Tigusigalpa\Ebay\Ebay setRefreshToken(string $token, int $expiresAt)
 * @method static string getConsentUrl(?array $scopes = null, ?string $state = null, ?string $locale = null)
 * @method static array exchangeCodeForToken(string $code)
 * @method static string getUserUrl(Site $site, string $username)
 * 
 * @see \Tigusigalpa\Ebay\Ebay
 */
class Ebay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ebay';
    }
}
