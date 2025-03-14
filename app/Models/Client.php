<?php

namespace App\Models;

use App\Models\Traits\HasOrganisationRelationship;
use Database\Factories\ClientFactory;
use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    use HasOrganisationRelationship;

    /**
     * The "booting" method of the model.
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($client) {
            if ($client->personal_access_client || $client->password_client) {
                return;
            }

            $client->organisation_id = Organisation::getDefault()->id;
        });
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ClientFactory::new();
    }
}
