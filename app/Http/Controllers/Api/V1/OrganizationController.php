<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function index(): JsonResponse
    {
        $organizations = Organization::query()
            ->where('is_active', true)
            ->withCount(['tournaments'])
            ->get();

        return response()->json([
            'data' => $organizations,
        ]);
    }

    public function show(Organization $organization): JsonResponse
    {
        return response()->json([
            'data' => $organization->load(['seasons', 'tournaments' => function ($q) {
                $q->where('is_public', true);
            }]),
        ]);
    }
}
