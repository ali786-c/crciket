package com.devwithguru.cricket.ui.navigation

sealed interface Screen {
    object Login : Screen
    object Onboarding : Screen
    object Home : Screen
    object CreateMatch : Screen
    object CreateTournament : Screen
    object MyTournaments : Screen
    data class TournamentHub(val tournamentId: String) : Screen
    data class TeamDetail(val teamId: String) : Screen
    data class Toss(val matchId: String, val homeTeam: String, val awayTeam: String) : Screen
    data class TossLineup(
        val matchId: String,
        val homeTeam: String,
        val awayTeam: String,
        val tossWinner: String,
        val tossDecision: String
    ) : Screen
    data class MatchCenter(
        val matchId: String,
        val isScorer: Boolean,
        val homeSquadList: List<String> = emptyList(),
        val awaySquadList: List<String> = emptyList()
    ) : Screen
    data class PlayerProfile(val playerId: String) : Screen
    object GlobalSearch : Screen
    data class MatchEditor(val matchId: String) : Screen
    object RecentMatches : Screen
}
