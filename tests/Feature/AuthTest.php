<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use Tigusigalpa\Ebay\EbayServiceProvider;
use Tigusigalpa\Ebay\Exceptions\AuthenticationException;
use Tigusigalpa\Ebay\Http\Auth;

class AuthTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [EbayServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('ebay.environment', 'sandbox');
        $app['config']->set('ebay.sandbox.app_id', 'test-app-id');
        $app['config']->set('ebay.sandbox.cert_id', 'test-cert-id');
        $app['config']->set('ebay.sandbox.runame', 'test-runame');
    }

    public function test_get_consent_url_generates_correct_url(): void
    {
        $auth = new Auth('sandbox', 'app-id', 'cert-id', 'runame');
        
        $url = $auth->getConsentUrl(['scope1', 'scope2'], 'state123');
        
        $this->assertStringContainsString('https://auth.sandbox.ebay.com/oauth2/authorize', $url);
        $this->assertStringContainsString('client_id=app-id', $url);
        $this->assertStringContainsString('redirect_uri=runame', $url);
        $this->assertStringContainsString('scope=scope1+scope2', $url);
        $this->assertStringContainsString('state=state123', $url);
    }

    public function test_get_access_token_by_code_success(): void
    {
        Http::fake([
            'api.sandbox.ebay.com/identity/v1/oauth2/token' => Http::response([
                'access_token' => 'test-access-token',
                'token_type' => 'Bearer',
                'expires_in' => 7200,
                'refresh_token' => 'test-refresh-token',
                'refresh_token_expires_in' => 47304000,
            ], 200),
        ]);

        $auth = new Auth('sandbox', 'app-id', 'cert-id', 'runame');
        $result = $auth->getAccessTokenByCode('auth-code-123');

        $this->assertEquals('test-access-token', $result['access_token']);
        $this->assertEquals('Bearer', $result['token_type']);
        $this->assertEquals('test-refresh-token', $result['refresh_token']);
        $this->assertArrayHasKey('expires_at', $result);
        $this->assertArrayHasKey('refresh_token_expires_at', $result);
    }

    public function test_get_access_token_by_code_failure(): void
    {
        Http::fake([
            'api.sandbox.ebay.com/identity/v1/oauth2/token' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'Authorization code is invalid',
            ], 400),
        ]);

        $auth = new Auth('sandbox', 'app-id', 'cert-id', 'runame');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Authorization code is invalid');

        $auth->getAccessTokenByCode('invalid-code');
    }

    public function test_refresh_access_token_success(): void
    {
        Http::fake([
            'api.sandbox.ebay.com/identity/v1/oauth2/token' => Http::response([
                'access_token' => 'new-access-token',
                'token_type' => 'Bearer',
                'expires_in' => 7200,
            ], 200),
        ]);

        $auth = new Auth('sandbox', 'app-id', 'cert-id', 'runame');
        $result = $auth->refreshAccessToken('refresh-token-123');

        $this->assertEquals('new-access-token', $result['access_token']);
        $this->assertArrayHasKey('expires_at', $result);
    }

    public function test_is_token_expired(): void
    {
        $auth = new Auth('sandbox', 'app-id', 'cert-id', 'runame');

        $pastTimestamp = now()->subHour()->timestamp;
        $futureTimestamp = now()->addHour()->timestamp;

        $this->assertTrue($auth->isTokenExpired($pastTimestamp));
        $this->assertFalse($auth->isTokenExpired($futureTimestamp));
    }
}
