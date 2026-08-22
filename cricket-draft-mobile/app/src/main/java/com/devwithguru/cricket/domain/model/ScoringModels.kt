package com.devwithguru.cricket.domain.model

data class BatterState(
    val name: String,
    val runs: Int = 0,
    val balls: Int = 0,
    val fours: Int = 0,
    val sixes: Int = 0,
    val isDismissed: Boolean = false,
    val dismissalType: String? = null,
    val bowlerName: String? = null,
    val fielderName: String? = null
)

data class BowlerState(
    val name: String,
    val balls: Int = 0,
    val runsConceded: Int = 0,
    val wickets: Int = 0
)

data class WicketEvent(
    val wicketNumber: Int,
    val batsmanName: String,
    val teamRuns: Int,
    val overs: String
)

data class PartnershipEvent(
    val wicketNumber: Int,
    val batter1Name: String,
    val batter2Name: String,
    val runs: Int,
    val balls: Int,
    val batter1ContributionRuns: Int = 0,
    val batter1ContributionBalls: Int = 0,
    val batter2ContributionRuns: Int = 0,
    val batter2ContributionBalls: Int = 0
)
