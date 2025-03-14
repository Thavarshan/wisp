<?php

namespace App\Models;

use App\Traits\HasOrganisationRelationship;
use Laravel\Passport\Client;
use Laravel\Passport\Token as PassportToken;

class Token extends PassportToken
{
    use HasOrganisationRelationship;

    /**
     * The "booting" method of the model.
     */
    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($token) {
            if (
                $token->client->personal_access_client
                || $token->client->password_client
            ) {
                return;
            }

            $client = Client::find(request()->input('client_id'));

            if ($client) {
                $token->organisation_id = $client->organisation_id;
            }
        });
    }
}
