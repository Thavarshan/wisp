<?php

namespace Tests\Feature\Multitenancy;

use App\Models\Client;
use App\Models\Organisation;
use App\Multitenancy\TenantFinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Multitenancy\TenantFinder\TenantFinder as AbstractTenantFinder;
use Tests\TestCase;

class TenantFinderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The tenant finder instance.
     */
    protected AbstractTenantFinder $tenantFinder;

    public function test_can_find_tenant_for_current_request(): void
    {
        $organisation = Organisation::factory()->create();

        $this->tenantFinder = app(TenantFinder::class);

        $client = Client::factory()->create([
            'name' => 'Test Client',
            'personal_access_client' => true,
            'organisation_id' => $organisation->id,
        ]);

        $request = Request::create('https://cerberus.test/api/v1/users', 'GET');
        $request->headers->set('X-Cerberus-Client-ID', $client->getKey());
        $request->headers->set('X-Cerberus-Client-Secret', $client->plainSecret);

        $tenant = $this->tenantFinder->findForRequest($request);

        $this->assertEquals($organisation->id, $tenant->id);
    }
}
