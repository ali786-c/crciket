package com.devwithguru.cricket.ui.screens

import androidx.compose.runtime.mutableStateListOf

data class ScheduledFixture(
    val id: String,
    val homeTeam: String,
    val awayTeam: String,
    val overs: Int,
    val ballType: String,
    val matchType: String,
    val wickets: Int,
    val venue: String,
    val date: String,
    val time: String,
    var status: String = "Scheduled", // "Scheduled", "Live", "Completed"
    var currentRuns: Int = 0,
    var currentWickets: Int = 0,
    var oversBowled: String = "0.0",
    var strikerName: String = "",
    var nonStrikerName: String = "",
    var bowlerName: String = "",
    var homeSquad: List<String> = emptyList(),
    var awaySquad: List<String> = emptyList(),
    var currentInnings: Int = 1,
    var firstInningsRuns: Int? = null,
    var firstInningsWickets: Int? = null,
    var firstInningsBatsmen: List<com.devwithguru.cricket.ui.screens.match.BatterState> = emptyList(),
    var firstInningsBowlers: List<com.devwithguru.cricket.ui.screens.match.BowlerState> = emptyList(),
    var secondInningsBatsmen: List<com.devwithguru.cricket.ui.screens.match.BatterState> = emptyList(),
    var secondInningsBowlers: List<com.devwithguru.cricket.ui.screens.match.BowlerState> = emptyList(),
    var firstInningsFOW: List<com.devwithguru.cricket.ui.screens.match.WicketEvent> = emptyList(),
    var firstInningsPartnerships: List<com.devwithguru.cricket.ui.screens.match.PartnershipEvent> = emptyList(),
    var secondInningsFOW: List<com.devwithguru.cricket.ui.screens.match.WicketEvent> = emptyList(),
    var secondInningsPartnerships: List<com.devwithguru.cricket.ui.screens.match.PartnershipEvent> = emptyList(),
    var activePartnershipRuns: Int = 0,
    var activePartnershipBalls: Int = 0,
    var firstInningsExtras: Int = 0,
    var secondInningsExtras: Int = 0,
    var firstInningsDotBalls: Int = 0,
    var secondInningsDotBalls: Int = 0
)

/**
 * Simple in-memory store for scheduled fixtures.
 * Shared between CreateMatchScreen (writes) and PlayerMatchesTab (reads).
 */
object FixtureStore {
    val fixtures = mutableStateListOf<ScheduledFixture>()
}
