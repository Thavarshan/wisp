<?php

namespace Tests\Feature\Authorisation;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Authorisation')]
class FetchRolesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
