<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDraftIsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $tournament = $request->route('tournament');

        if ($tournament && !$tournament->has_draft) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Drafting is not enabled for this tournament.'], 403);
            }
            abort(403, 'Drafting is not enabled for this tournament.');
        }

        return $next($request);
    }
}
