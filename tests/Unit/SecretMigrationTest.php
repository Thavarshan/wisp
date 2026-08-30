<?php

namespace Tests\Unit;

use RuntimeException;
use Tests\TestCase;

class SecretMigrationTest extends TestCase
{
    public function test_secret_modernization_migration_is_explicitly_irreversible(): void
    {
        $migration = require base_path('database/migrations/2026_08_29_000000_modernize_secrets_table.php');

        $this->expectException(RuntimeException::class);
        $migration->down();
    }

    public function test_secure_secret_storage_migration_is_explicitly_irreversible(): void
    {
        $migration = require base_path('database/migrations/2026_08_30_000000_secure_secret_storage.php');

        $this->expectException(RuntimeException::class);
        $migration->down();
    }
}
