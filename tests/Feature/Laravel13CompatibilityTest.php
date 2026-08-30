<?php

namespace Tests\Feature;

use App\Http\Controllers\SecretController;
use App\Models\Secret;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Exceptions\OriginMismatchException;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class Laravel13CompatibilityTest extends TestCase
{
    #[Test]
    public function controller_middleware_attributes_preserve_all_limiters(): void
    {
        $expected = [
            'store' => ['throttle:secret-creation'],
            'show' => ['throttle:secret-access'],
            'reveal' => [
                'throttle:secret-reveal-ip',
                'throttle:secret-reveal-secret',
            ],
            'revoke' => ['throttle:secret-revocation'],
        ];

        foreach ($expected as $method => $middleware) {
            $attributes = new ReflectionMethod(
                SecretController::class,
                $method,
            )->getAttributes(Middleware::class);

            $this->assertSame(
                $middleware,
                array_map(
                    fn (object $attribute): string => $attribute
                        ->newInstance()
                        ->middleware,
                    $attributes,
                ),
            );
        }
    }

    #[Test]
    public function security_configuration_uses_laravel_13_safe_defaults(): void
    {
        $this->assertSame('json', config('session.serialization'));
        $this->assertFalse(config('cache.serializable_classes'));
    }

    #[Test]
    public function public_secret_routes_keep_their_existing_urls(): void
    {
        $this->assertSame('/secrets', route('secrets.store', absolute: false));
        $this->assertSame(
            '/secrets/access-token',
            route('secrets.show', ['token' => 'access-token'], false),
        );
        $this->assertSame(
            '/secrets/access-token/reveal',
            route('secrets.reveal', ['token' => 'access-token'], false),
        );
        $this->assertSame(
            '/secrets/access-token',
            route('secrets.revoke', ['token' => 'access-token'], false),
        );
    }

    #[Test]
    public function secret_attributes_preserve_mass_assignment_and_hidden_values(): void
    {
        $secret = Secret::factory()->make([
            'content' => 'private content',
            'password' => 'private password',
        ]);

        $this->assertSame(
            [
                'access_token_hash',
                'revocation_token_hash',
                'content',
                'password',
                'expired_at',
            ],
            $secret->getFillable(),
        );
        $this->assertSame(
            [
                'access_token_hash',
                'revocation_token_hash',
                'content',
                'password',
            ],
            $secret->getHidden(),
        );
        $this->assertNotContains('content', array_keys($secret->toArray()));
        $this->assertNotContains('password', array_keys($secret->toArray()));
        $this->assertNotContains(
            'access_token_hash',
            array_keys($secret->toArray()),
        );
        $this->assertNotContains(
            'revocation_token_hash',
            array_keys($secret->toArray()),
        );
        $this->assertCount(
            1,
            new ReflectionMethod(Secret::class, 'withAccessToken')
                ->getAttributes(Scope::class),
        );
        $this->assertCount(
            1,
            new \ReflectionClass(Secret::class)->getAttributes(Fillable::class),
        );
        $this->assertCount(
            1,
            new \ReflectionClass(Secret::class)->getAttributes(Hidden::class),
        );
    }

    #[Test]
    public function origin_only_forgery_protection_accepts_same_origin_and_rejects_cross_site(): void
    {
        PreventRequestForgery::useOriginOnly(true);

        try {
            $application = \Mockery::mock(Application::class);
            $application->shouldReceive('runningInConsole')->andReturnFalse();
            $application->shouldReceive('runningUnitTests')->andReturnFalse();

            $middleware = new PreventRequestForgery(
                $application,
                app(Encrypter::class),
            );
            $next = fn () => response()->noContent();

            $sameOrigin = Request::create('/secrets', 'POST');
            $sameOrigin->headers->set('Sec-Fetch-Site', 'same-origin');

            $this->assertSame(
                204,
                $middleware->handle($sameOrigin, $next)->getStatusCode(),
            );

            $crossSite = Request::create('/secrets', 'POST');
            $crossSite->headers->set('Sec-Fetch-Site', 'cross-site');

            $this->expectException(OriginMismatchException::class);
            $middleware->handle($crossSite, $next);
        } finally {
            PreventRequestForgery::useOriginOnly(false);
        }
    }
}
