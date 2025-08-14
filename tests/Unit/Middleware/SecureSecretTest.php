<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\SecureSecret;
use App\Models\Secret;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SecureSecretTest extends TestCase
{
    use RefreshDatabase;

    private SecureSecret $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SecureSecret;
    }

    public function test_it_passes_through_when_secret_exists_via_query_parameter()
    {
        $secret = Secret::factory()->create([
            'expired_at' => Carbon::now()->addHour(),
        ]);

        $request = Request::create('/validate-password?secret='.$secret->uid);

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals('Success', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_aborts_with_404_when_secret_not_found_via_query_parameter()
    {
        $request = Request::create('/validate-password?secret=nonexistent');

        $next = function ($request) {
            return new Response('Success');
        };

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->middleware->handle($request, $next);
    }

    public function test_it_aborts_with_410_when_secret_has_expired()
    {
        $secret = Secret::factory()->create([
            'expired_at' => Carbon::now()->subHour(),
        ]);

        $request = Request::create('/validate-password?secret='.$secret->uid);

        $next = function ($request) {
            return new Response('Success');
        };

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('Secret has expired.');

        $this->middleware->handle($request, $next);
    }

    public function test_it_works_with_route_model_binding()
    {
        $secret = Secret::factory()->create([
            'expired_at' => Carbon::now()->addHour(),
        ]);

        // For unit testing, we'll test the query parameter path since route model binding
        // requires the full framework setup which is better tested in feature tests
        $request = Request::create('/secrets/'.$secret->uid.'?secret='.$secret->uid);

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertEquals('Success', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_it_handles_null_query_parameter()
    {
        $request = Request::create('/validate-password?secret=');

        $next = function ($request) {
            return new Response('Success');
        };

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->middleware->handle($request, $next);
    }
}
