<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Services\UnifiedSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request, UnifiedSearchService $searchService): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $query = $request->query('q', '');
        return response()->json([
            'data' => $searchService->search($query),
        ]);
    }
}
