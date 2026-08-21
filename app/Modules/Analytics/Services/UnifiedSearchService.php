<?php

namespace App\Modules\Analytics\Services;

use App\Models\CricketMatch;
use App\Models\PlayerProfile;
use App\Models\Team;
use App\Models\Tournament;

class UnifiedSearchService
{
    public function search(string $q): array
    {
        if (strlen($q) < 2) {
            return [
                'players' => [],
                'teams' => [],
                'tournaments' => [],
                'matches' => [],
            ];
        }

        $players = PlayerProfile::query()
            ->where('full_name', 'like', "%{$q}%")
            ->where('is_active', true)
            ->take(10)
            ->get(['id', 'full_name', 'playing_role', 'city']);

        $teams = Team::query()
            ->where('name', 'like', "%{$q}%")
            ->orWhere('short_name', 'like', "%{$q}%")
            ->take(10)
            ->get(['id', 'name', 'short_name', 'logo_path']);

        $tournaments = Tournament::query()
            ->where('name', 'like', "%{$q}%")
            ->take(10)
            ->get(['id', 'name', 'slug', 'starts_on', 'ends_on']);

        $matches = CricketMatch::query()
            ->whereHas('homeTeam', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")->orWhere('short_name', 'like', "%{$q}%");
            })
            ->orWhereHas('awayTeam', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")->orWhere('short_name', 'like', "%{$q}%");
            })
            ->with(['homeTeam', 'awayTeam'])
            ->take(10)
            ->get()
            ->map(function ($match) {
                return [
                    'id' => $match->id,
                    'status' => $match->status,
                    'starts_on' => $match->starts_on,
                    'home_team' => $match->homeTeam?->short_name,
                    'away_team' => $match->awayTeam?->short_name,
                ];
            });

        return [
            'players' => $players->toArray(),
            'teams' => $teams->toArray(),
            'tournaments' => $tournaments->toArray(),
            'matches' => $matches->toArray(),
        ];
    }
}
