package com.devwithguru.cricket.data.api

import retrofit2.Response
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.Path

/**
 * Retrofit API service for Cricket Draft backend.
 * Base URL is configured in NetworkModule.
 */
interface ApiService {

    // ─── Auth Endpoints ─────────────────────────────────────

    /**
     * Login with email/password.
     * POST /api/v1/auth/login
     */
    @POST("api/v1/auth/login")
    suspend fun login(
        @Body request: LoginRequest
    ): Response<LoginResponse>

    /**
     * Get current user profile.
     * GET /api/v1/auth/me
     */
    @GET("api/v1/auth/me")
    suspend fun getMe(
        @Header("Authorization") token: String
    ): Response<UserResponse>

    /**
     * Update player profile.
     * PATCH /api/v1/profile
     */
    @retrofit2.http.PATCH("api/v1/profile")
    suspend fun updateProfile(
        @Header("Authorization") token: String,
        @Body request: UpdateProfileRequest
    ): Response<ProfileResponse>

    // ─── Tournament Endpoints (Public) ─────────────────────

    /**
     * List all public tournaments.
     * GET /api/v1/tournaments
     */
    @GET("api/v1/tournaments")
    suspend fun getTournaments(): Response<TournamentListResponse>

    /**
     * Get tournament details.
     * GET /api/v1/tournaments/{tournamentId}
     */
    @GET("api/v1/tournaments/{tournamentId}")
    suspend fun getTournament(
        @Path("tournamentId") tournamentId: String
    ): Response<TournamentDetailResponse>

    /**
     * Get tournament teams.
     * GET /api/v1/tournaments/{tournamentId}/teams
     */
    @GET("api/v1/tournaments/{tournamentId}/teams")
    suspend fun getTournamentTeams(
        @Path("tournamentId") tournamentId: String
    ): Response<TournamentTeamsResponse>

    /**
     * Get tournament players.
     * GET /api/v1/tournaments/{tournamentId}/players
     */
    @GET("api/v1/tournaments/{tournamentId}/players")
    suspend fun getTournamentPlayers(
        @Path("tournamentId") tournamentId: String
    ): Response<TournamentPlayersResponse>

    /**
     * Get tournament standings.
     * GET /api/v1/tournaments/{tournamentId}/standings
     */
    @GET("api/v1/tournaments/{tournamentId}/standings")
    suspend fun getTournamentStandings(
        @Path("tournamentId") tournamentId: String
    ): Response<TournamentStandingsResponse2>

    /**
     * Get tournament fixtures.
     * GET /api/v1/tournaments/{tournamentId}/fixtures
     */
    @GET("api/v1/tournaments/{tournamentId}/fixtures")
    suspend fun getTournamentFixtures(
        @Path("tournamentId") tournamentId: String
    ): Response<TournamentFixturesResponse2>

    // ─── Team Endpoints ────────────────────────────────────

    /**
     * Get team squad.
     * GET /api/v1/teams/{teamId}/squad
     */
    @GET("api/v1/teams/{teamId}/squad")
    suspend fun getTeamSquad(
        @Path("teamId") teamId: String
    ): Response<TeamSquadResponse>

    // ─── Match Endpoints ────────────────────────────────────

    /**
     * Get full match state including innings, batting, bowling, recent deliveries.
     * GET /api/v1/matches/{matchId}/state
     */
    @GET("api/v1/matches/{matchId}/state")
    suspend fun getMatchState(
        @Path("matchId") matchId: String,
        @Header("Authorization") token: String? = null
    ): Response<MatchStateResponse>

    /**
     * Get MVP data for a match.
     * GET /api/v1/matches/{matchId}/mvp
     */
    @GET("api/v1/matches/{matchId}/mvp")
    suspend fun getMatchMvp(
        @Path("matchId") matchId: String,
        @Header("Authorization") token: String? = null
    ): Response<MvpResponse>

    // ─── Player Endpoints ───────────────────────────────────

    /**
     * Get player stats.
     * GET /api/v1/players/{playerId}/stats
     */
    @GET("api/v1/players/{playerId}/stats")
    suspend fun getPlayerStats(
        @Path("playerId") playerId: String,
        @Header("Authorization") token: String? = null
    ): Response<PlayerStatsApiResponse>

    /**
     * Get player insights.
     * GET /api/v1/players/{playerId}/insights
     */
    @GET("api/v1/players/{playerId}/insights")
    suspend fun getPlayerInsights(
        @Path("playerId") playerId: String,
        @Header("Authorization") token: String? = null
    ): Response<PlayerInsightsApiResponse>

    /**
     * Get player's match history.
     * GET /api/v1/players/{playerId}/matches
     */
    @GET("api/v1/players/{playerId}/matches")
    suspend fun getPlayerMatches(
        @Path("playerId") playerId: String,
        @Header("Authorization") token: String? = null
    ): Response<PlayerMatchesResponse>

    /**
     * Get player's teams.
     * GET /api/v1/players/{playerId}/teams
     */
    @GET("api/v1/players/{playerId}/teams")
    suspend fun getPlayerTeams(
        @Path("playerId") playerId: String,
        @Header("Authorization") token: String? = null
    ): Response<PlayerTeamsResponse>
}
