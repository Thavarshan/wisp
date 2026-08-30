<?php

namespace Database\Factories;

use App\Models\Secret;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Secret>
 */
class SecretFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function definition(): array
    {
        return [
            'access_token_hash' => Secret::hashToken(bin2hex(random_bytes(32))),
            'revocation_token_hash' => Secret::hashToken(bin2hex(random_bytes(32))),
            'content' => $this->faker->text,
            'password' => null,
            'expired_at' => $this->faker->dateTimeBetween('now', '+1 week'),
        ];
    }
}
