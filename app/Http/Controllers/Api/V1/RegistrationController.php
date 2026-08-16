<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function mine(Request $request, Tournament $tournament): JsonResponse
    {
        $profile = $request->user()->playerProfile;
        $registration = $profile ? $tournament->tournamentPlayers()->where('player_profile_id', $profile->id)->first() : null;
        return response()->json(['data' => $registration ? ['id' => $registration->id, 'status' => $registration->status, 'submitted_at' => $registration->created_at?->toIso8601String(), 'reviewed_at' => $registration->reviewed_at?->toIso8601String()] : null]);
    }

    public function store(Request $request, Tournament $tournament): JsonResponse
    {
        $profile = $request->user()->playerProfile;
        abort_unless($profile, 422, 'Complete your player profile before registering.');
        abort_unless($tournament->publiclyVisibleNow() && in_array($tournament->status, ['registration', 'ready'], true), 409, 'This tournament is not accepting registrations.');
        $registration = $tournament->tournamentPlayers()->updateOrCreate(['player_profile_id' => $profile->id], ['status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null]);
        return response()->json(['data' => ['id' => $registration->id, 'status' => $registration->status], 'message' => 'Registration submitted for admin approval.']);
    }
}
