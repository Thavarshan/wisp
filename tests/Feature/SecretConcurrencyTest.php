<?php

namespace Tests\Feature;

use App\Actions\CreateSecret;
use App\Actions\RevealSecret;
use App\Enums\ExpirationOption;
use Illuminate\Concurrency\ConcurrencyManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Throwable;

class SecretConcurrencyTest extends TestCase
{
    public function test_concurrent_reveals_have_exactly_one_successful_result(): void
    {
        if (! class_exists(ConcurrencyManager::class)) {
            $this->markTestSkipped('Laravel concurrency is unavailable.');
        }

        $databasePath = tempnam(sys_get_temp_dir(), 'wisp-concurrency-');
        $this->assertIsString($databasePath);
        config(['database.connections.sqlite.database' => $databasePath]);
        DB::purge('sqlite');

        Artisan::call('migrate', ['--database' => 'sqlite', '--force' => true]);
        $created = app(CreateSecret::class)->handle([
            'content' => 'concurrent value',
            'expiration' => ExpirationOption::ONE_DAY->value,
            'password' => null,
        ]);
        DB::disconnect('sqlite');

        try {
            $results = Concurrency::driver('process')->run([
                function () use ($created, $databasePath): array {
                    try {
                        config(['database.connections.sqlite.database' => $databasePath]);
                        DB::purge('sqlite');

                        return ['success' => true, 'content' => app(RevealSecret::class)->handle($created['access_token'])];
                    } catch (Throwable $exception) {
                        return ['success' => false, 'exception' => $exception::class, 'message' => $exception->getMessage()];
                    }
                },
                function () use ($created, $databasePath): array {
                    try {
                        config(['database.connections.sqlite.database' => $databasePath]);
                        DB::purge('sqlite');

                        return ['success' => true, 'content' => app(RevealSecret::class)->handle($created['access_token'])];
                    } catch (Throwable $exception) {
                        return ['success' => false, 'exception' => $exception::class, 'message' => $exception->getMessage()];
                    }
                },
            ]);
        } finally {
            @unlink($databasePath);
            config(['database.connections.sqlite.database' => ':memory:']);
            DB::purge('sqlite');
        }

        $successful = array_values(array_filter($results, fn (array $result): bool => $result['success']));
        $failed = array_values(array_filter($results, fn (array $result): bool => ! $result['success']));

        $this->assertCount(1, $successful);
        $this->assertSame('concurrent value', $successful[0]['content']);
        $this->assertCount(1, $failed);
    }
}
