<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CricketMatch;
use App\Services\MatchResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminResultController extends Controller
{
    public function __construct(private readonly MatchResultService $results)
    {
    }

    public function submit(Request $request, CricketMatch $match): JsonResponse
    {
        return response()->json(['data' => $this->results->submit($match, (int) $request->user()->id), 'message' => 'Match result submitted for approval.']);
    }

    public function approve(Request $request, CricketMatch $match): JsonResponse
    {
        return response()->json(['data' => $this->results->approve($match, (int) $request->user()->id), 'message' => 'Match result approved and standings rebuilt.']);
    }
}
