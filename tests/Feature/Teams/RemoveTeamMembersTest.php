<?php

namespace Tests\Feature\Teams;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('Teams')]
class RemoveTeamMembersTest extends TestCase
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
