<?php

namespace App\Multitenancy;

use App\Models\Client;
use App\Models\Organisation;
use App\Models\Token;
use Illuminate\Http\Request;
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
        // If request is authenticated and has access token
        if ($request->user() && $request->bearerToken()) {
            $tokenId = $this->parser
                ->parse($request->bearerToken())
                ->headers()
                ->get('jti');

            $token = Token::find($tokenId);

            if ($token) {
                return Organisation::find($token->organisation_id);
            }
        }

        // If the request body has a client_id
        if ($request->has('client_id')) {
            return Organisation::find(
                Client::find($request->client_id)->organisation_id
            );
        }

        // Default organisation
        return Organisation::getDefault();
    }
}
