<?php

namespace Tests;

use App\Models\Client;
use App\Models\Organisation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;

abstract class TestCase extends BaseTestCase
{
    /**
     * The without seeding permissions flag.
     */
    protected bool $withoutSeedingPermissions = false;

    /**
     * The organisation instance.
     */
    protected ?Organisation $organisation = null;

    /**
     * The actor user instance.
     */
    protected ?User $actor = null;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $organisation = $this->getOrganisation();

        if (! $this->withoutSeedingPermissions) {
            $this->seed(RolePermissionSeeder::class);
        }

        $this->actor = User::factory()
            ->for($organisation)
            ->create();
    }

    /**
     * Sign in the given user or create one and sign in.
     */
    public function signIn(?User $user = null): self
    {
        $user = $user ?: User::factory()
            ->for($this->getOrganisation())
            ->create();

        Passport::actingAs($user);

        return $this;
    }

    /**
     * Sign in the given user or create one and sign in.
     */
    public function signInAsClient(?array $scopes = []): self
    {
        $client = Client::factory()
            ->for($this->organisation)
            ->create();

        Passport::actingAsClient($client, $scopes);

        return $this;
    }

    /**
     * Assign the given role to the actor.
     */
    protected function getOrganisation(): Organisation
    {
        if (is_null($this->organisation)) {
            $this->organisation = Organisation::getDefault();
        }

        return $this->organisation;
    }
}
