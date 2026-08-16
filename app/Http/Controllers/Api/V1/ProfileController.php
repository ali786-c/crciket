<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PlayerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->payload($request->user()->playerProfile)]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:100'],
            'playing_role' => ['required', 'string', 'in:Batter,Bowler,All-rounder,Wicketkeeper'],
            'batting_style' => ['nullable', 'string', 'max:100'],
            'bowling_style' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);
        $profile = PlayerProfile::updateOrCreate(['user_id' => $request->user()->id], [...$data, 'is_active' => true]);
        return response()->json(['data' => $this->payload($profile), 'message' => 'Player profile saved successfully.']);
    }

    private function payload(?PlayerProfile $profile): ?array
    {
        if (! $profile) return null;
        return ['id' => $profile->id, 'full_name' => $profile->full_name, 'phone' => $profile->phone, 'city' => $profile->city, 'playing_role' => $profile->playing_role, 'batting_style' => $profile->batting_style, 'bowling_style' => $profile->bowling_style, 'bio' => $profile->bio, 'is_active' => $profile->is_active];
    }
}
