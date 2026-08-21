<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    public function index(): JsonResponse
    {
        $articles = NewsArticle::published()
            ->with('creator:id,name')
            ->latest('published_at')
            ->paginate(15);

        return response()->json($articles);
    }

    public function show(string $slug): JsonResponse
    {
        $article = NewsArticle::published()
            ->where('slug', $slug)
            ->with('creator:id,name')
            ->firstOrFail();

        return response()->json([
            'data' => $article,
        ]);
    }
}
