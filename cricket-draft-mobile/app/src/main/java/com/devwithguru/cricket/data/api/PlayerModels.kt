package com.devwithguru.cricket.data.api

import retrofit2.Response
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.Path

// ─── Player Stats Response ──────────────────────────────────

data class PlayerStatsApiResponse(
    val data: PlayerStatsDetailData
)

data class PlayerStatsDetailData(
    val player_name: String?,
    val matches: Int,
    val runs: Int,
    val wickets: Int,
    val batting_average: Double?,
    val bowling_average: Double?,
    val strike_rate: Double?,
    val economy: Double?,
    val fifties: Int? = null,
    val hundreds: Int? = null,
    val best_bowling: String? = null,
    val catches: Int? = null,
    val stumpings: Int? = null
)

// ─── Player Insights Response ───────────────────────────────

data class PlayerInsightsApiResponse(
    val data: PlayerInsightsDetailData
)

data class PlayerInsightsDetailData(
    val recent_form: List<RecentFormData>,
    val vs_teams: List<VsTeamPerformance>,
    val favorite_venues: List<VenuePerformance>?,
    val batting_by_phase: Map<String, Int>?
)

data class RecentFormData(
    val match_id: Int,
    val opponent: String?,
    val runs: Int?,
    val wickets: Int?,
    val date: String?,
    val venue: String?
)

data class VsTeamPerformance(
    val team_name: String?,
    val matches: Int,
    val runs: Int,
    val wickets: Int,
    val average: Double?
)

data class VenuePerformance(
    val venue: String?,
    val matches: Int,
    val runs: Int,
    val average: Double?
)

// ─── Player Matches Response ────────────────────────────────

data class PlayerMatchesResponse(
    val data: List<PlayerMatchData>
)

data class PlayerMatchData(
    val id: Int,
    val tournament_name: String?,
    val opponent: String?,
    val venue: String?,
    val date: String?,
    val runs: Int?,
    val wickets: Int?,
    val overs_bowled: String?,
    val result: String?,
    val match_type: String?
)

// ─── Player Teams Response ──────────────────────────────────

data class PlayerTeamsResponse(
    val data: List<PlayerTeamData>
)

data class PlayerTeamData(
    val team_id: Int,
    val team_name: String?,
    val tournament_name: String?,
    val role: String?,
    val is_captain: Boolean?,
    val matches_played: Int?
)
