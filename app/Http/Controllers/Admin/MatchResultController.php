<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CricketMatch;
use App\Modules\Scoring\Services\MatchResultService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MatchResultController extends Controller
{
    public function __construct(private readonly MatchResultService $results)
    {
    }

    public function submit(Request $request, CricketMatch $match): RedirectResponse
    {
        $this->results->submit($match, (int) $request->user()->id);
        return back()->with('status', 'Result submitted for approval.');
    }

    public function approve(Request $request, CricketMatch $match): RedirectResponse
    {
        $this->results->approve($match, (int) $request->user()->id);
        return back()->with('status', 'Result approved and standings rebuilt.');
    }
}
