<?php

namespace App\Multitenancy;

use App\Models\Client;
use App\Models\Organisation;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Parser as JWTParser;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder as AbstractTenantFinder;

class TenantFinder extends AbstractTenantFinder
{
    /**
     * Create a new tenant finder instance.
     *
     * @return void
     */
    public function __construct(protected JWTParser $parser) {}

    /**
     * {@inheritdoc}
     */
    public function findForRequest(Request $request): ?IsTenant
    {
        if ($tenant = $this->findTenantByToken($request)) {
            return $tenant;
        }

        if ($tenant = $this->findTenantByClientId($request)) {
            return $tenant;
        }

        return Organisation::getDefault();
    }

    /**
     * Find a tenant by the given token.
     */
    protected function findTenantByToken(Request $request): ?IsTenant
    {
        if (is_null($request->bearerToken())) {
            return null;
        }

        $tokenId = $this->parser
            ->parse($request->bearerToken())
            ->headers()
            ->get('jti');

        $token = Token::find($tokenId);

        return $token ? Organisation::find($token->organisation_id) : null;
    }

    /**
     * Find a tenant by the given client id.
     */
    protected function findTenantByClientId(Request $request): ?IsTenant
    {
        if (! $request->hasHeader('X-Cerberus-Client-ID')) {
            return null;
        }

        $client = Client::find($request->header('X-Cerberus-Client-ID'));

        if (! Hash::check($request->header('X-Cerberus-Client-Secret'), $client->secret)) {
            return null;
        }

        return $client ? Organisation::find($client->organisation_id) : null;
    }
}
