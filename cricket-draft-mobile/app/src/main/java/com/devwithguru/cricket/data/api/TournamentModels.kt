package com.devwithguru.cricket.data.api

import retrofit2.Response
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.Path

// ─── Tournament Response Models ─────────────────────────────

data class TournamentListResponse(
    val data: List<TournamentData>
)

data class TournamentDetailResponse(
    val data: TournamentData
)

data class TournamentData(
    val id: Int,
    val name: String?,
    val season_name: String?,
    val slug: String?,
    val status: String?,
    val venue: String?,
    val city: String?,
    val timezone: String?,
    val starts_on: String?,
    val ends_on: String?,
    val rule_profile: RuleProfileData?,
    val fixtures_count: Int? = null
)

data class RuleProfileData(
    val name: String?,
    val format: String?,
    val overs_per_innings: Int?,
    val legal_balls_per_over: Int?
)

data class TournamentTeamsResponse(
    val data: List<TournamentTeamData>
)

data class TournamentTeamData(
    val id: Int,
    val name: String?,
    val short_name: String?,
    val logo_path: String?,
    val squad_count: Int?
)

data class TournamentPlayersResponse(
    val data: List<TournamentPlayerData>
)

data class TournamentPlayerData(
    val id: Int,
    val full_name: String?,
    val playing_role: String?,
    val city: String?
)

data class TournamentStandingsResponse2(
    val data: List<TournamentStandingData>
)

data class TournamentStandingData(
    val position: Int?,
    val team: TeamData?,
    val played: Int,
    val wins: Int,
    val losses: Int,
    val ties: Int,
    val no_results: Int,
    val points: Int,
    val net_run_rate: Double?
)

data class TournamentFixturesResponse2(
    val data: List<TournamentFixtureData2>
)

data class TournamentFixtureData2(
    val id: Int,
    val round_number: Int?,
    val round_name: String?,
    val match_number: Int?,
    val scheduled_at: String?,
    val timezone: String?,
    val venue: String?,
    val city: String?,
    val status: String?,
    val home_team: TeamData?,
    val away_team: TeamData?,
    val match_id: Int?,
    val match_status: String?
)

// ─── Team Squad Response ────────────────────────────────────

data class TeamSquadResponse(
    val data: TeamSquadData
)

data class TeamSquadData(
    val team_name: String?,
    val squad: List<SquadPlayerData>
)

data class SquadPlayerData(
    val tournament_player_id: Int,
    val player_name: String?,
    val playing_role: String?,
    val is_captain: Boolean?,
    val is_vice_captain: Boolean?,
    val is_wicketkeeper: Boolean?
)
