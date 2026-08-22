package com.devwithguru.cricket.data.api

import com.devwithguru.cricket.domain.model.BatterState
import com.devwithguru.cricket.domain.model.BowlerState
import com.devwithguru.cricket.domain.model.ScheduledFixture

/**
 * API response from GET /api/v1/matches/{matchId}/state
 */
data class MatchStateResponse(
    val data: MatchStateData
)

data class MatchStateData(
    val id: Int,
    val revision: Int,
    val status: String,
    val result_type: String?,
    val result_summary: String?,
    val winner_team_id: Int?,
    val overs_per_innings: Int,
    val innings: List<InningsData>
)

data class InningsData(
    val id: Int,
    val number: Int,
    val batting_team: TeamData?,
    val bowling_team: TeamData?,
    val runs: Int,
    val wickets: Int,
    val legal_balls: Int,
    val maximum_overs: Int,
    val overs: String,
    val target: Int?,
    val status: String,
    val batting: List<BattingStatData>,
    val bowling: List<BowlingStatData>,
    val recent_deliveries: List<RecentDeliveryData>
)

data class TeamData(
    val id: Int?,
    val name: String?,
    val short_name: String?
)

data class BattingStatData(
    val player: String?,
    val dismissal: String?,
    val runs: Int,
    val balls: Int,
    val fours: Int,
    val sixes: Int,
    val strike_rate: Double
)

data class BowlingStatData(
    val player: String?,
    val overs: String,
    val runs: Int,
    val wickets: Int,
    val wides: Int,
    val no_balls: Int,
    val economy: Double
)

data class RecentDeliveryData(
    val over: String,
    val notation: String,
    val total_runs: Int
)

/**
 * API response from GET /api/v1/matches/{matchId}/mvp
 */
data class MvpResponse(
    val data: List<MvpPlayerData>
)

data class MvpPlayerData(
    val player_name: String,
    val team_name: String?,
    val total_points: Int,
    val batting_points: Int,
    val bowling_points: Int,
    val fielding_points: Int
)

/**
 * Convert API response to domain ScheduledFixture
 */
fun MatchStateData.toScheduledFixture(matchId: String): ScheduledFixture {
    val firstInnings = innings.find { it.number == 1 }
    val secondInnings = innings.find { it.number == 2 }

    val homeTeamName = firstInnings?.batting_team?.name ?: "Team A"
    val awayTeamName = firstInnings?.bowling_team?.name ?: "Team B"

    return ScheduledFixture(
        id = matchId,
        homeTeam = homeTeamName,
        awayTeam = awayTeamName,
        overs = overs_per_innings,
        ballType = "Leather",
        matchType = "T20",
        wickets = 10,
        venue = "",
        date = "",
        time = "",
        status = when (status) {
            "live" -> "Live"
            "completed", "result_pending", "approved" -> "Completed"
            else -> "Scheduled"
        },
        currentRuns = secondInnings?.runs ?: 0,
        currentWickets = secondInnings?.wickets ?: 0,
        oversBowled = secondInnings?.overs ?: "0.0",
        currentInnings = if (secondInnings != null) 2 else 1,
        firstInningsRuns = firstInnings?.runs,
        firstInningsWickets = firstInnings?.wickets,
        firstInningsBatsmen = firstInnings?.batting?.map { it.toBatterState() } ?: emptyList(),
        firstInningsBowlers = firstInnings?.bowling?.map { it.toBowlerState() } ?: emptyList(),
        secondInningsBatsmen = secondInnings?.batting?.map { it.toBatterState() } ?: emptyList(),
        secondInningsBowlers = secondInnings?.bowling?.map { it.toBowlerState() } ?: emptyList()
    )
}

fun BattingStatData.toBatterState() = BatterState(
    name = player ?: "Unknown",
    runs = runs,
    balls = balls,
    fours = fours,
    sixes = sixes,
    isDismissed = !dismissal.isNullOrEmpty() && dismissal != "not_out",
    dismissalType = dismissal
)

fun BowlingStatData.toBowlerState() = BowlerState(
    name = player ?: "Unknown",
    balls = runBallsFromOvers(overs),
    runsConceded = runs,
    wickets = wickets
)

private fun runBallsFromOvers(overs: String): Int {
    val parts = overs.split(".")
    val fullOvers = parts.getOrElse(0) { "0" }.toIntOrNull() ?: 0
    val balls = parts.getOrElse(1) { "0" }.toIntOrNull() ?: 0
    return fullOvers * 6 + balls
}
