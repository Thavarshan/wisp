<?php

namespace Tests\Unit\Http\Requests\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_authorizes_all_requests()
    {
        $request = new LoginRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_has_correct_validation_rules()
    {
        $request = new LoginRequest;

        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertContains('required', $rules['email']);
        $this->assertContains('email', $rules['email']);
        $this->assertContains('required', $rules['password']);
        $this->assertContains('string', $rules['password']);
    }

    public function test_authenticate_succeeds_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $request->authenticate();

        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_authenticate_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->expectException(ValidationException::class);

        $request->authenticate();

        $this->assertFalse(Auth::check());
    }

    public function test_it_generates_throttle_key_correctly()
    {
        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'Test@Example.com',
        ], [], [], [
            'REMOTE_ADDR' => '192.168.1.1',
        ]);

        $throttleKey = $request->throttleKey();

        $this->assertStringContainsString('test@example.com', $throttleKey);
        $this->assertStringContainsString('192.168.1.1', $throttleKey);
        $this->assertStringContainsString('|', $throttleKey);
    }

    public function test_it_clears_rate_limiter_on_successful_authentication()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Simulate some failed attempts first
        RateLimiter::hit($request->throttleKey());

        $request->authenticate();

        // The rate limiter should be cleared after successful authentication
        $this->assertEquals(0, RateLimiter::attempts($request->throttleKey()));
    }

    public function test_ensure_is_not_rate_limited_throws_exception_when_too_many_attempts()
    {
        Event::fake();

        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Hit the rate limiter 6 times (limit is 5)
        for ($i = 0; $i < 6; $i++) {
            RateLimiter::hit($request->throttleKey());
        }

        $this->expectException(ValidationException::class);

        $request->ensureIsNotRateLimited();

        Event::assertDispatched(Lockout::class);
    }

    public function test_ensure_is_not_rate_limited_passes_when_under_limit()
    {
        $request = LoginRequest::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Hit the rate limiter 3 times (under the limit of 5)
        for ($i = 0; $i < 3; $i++) {
            RateLimiter::hit($request->throttleKey());
        }

        // Should not throw an exception
        $request->ensureIsNotRateLimited();

        $this->assertTrue(true); // If we reach here, no exception was thrown
    }

    protected function tearDown(): void
    {
        RateLimiter::clear('*');
        parent::tearDown();
    }
}
