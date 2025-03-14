<?php

namespace App\Http\Controllers;

use App\Actions\OnboardNewOrganisation;
use App\Http\Requests\OnboardingRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class OnboardingController extends Controller
{
    /**
     * Onboard a new organisation.
     */
    public function __invoke(
        OnboardingRequest $request,
        OnboardNewOrganisation $onboarder
    ): JsonResponse {
        $result = $onboarder->onboard($request->validated());

        return response()->json(
            ['message' => $result['message']],
            $result['status']
        );
    }
}
