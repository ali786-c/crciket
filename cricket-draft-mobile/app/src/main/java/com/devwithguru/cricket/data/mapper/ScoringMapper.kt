package com.devwithguru.cricket.data.mapper

import com.devwithguru.cricket.data.db.entity.BatterStatsEntity
import com.devwithguru.cricket.data.db.entity.BowlerStatsEntity
import com.devwithguru.cricket.data.db.entity.PartnershipEventEntity
import com.devwithguru.cricket.data.db.entity.WicketEventEntity
import com.devwithguru.cricket.domain.model.BatterState
import com.devwithguru.cricket.domain.model.BowlerState
import com.devwithguru.cricket.domain.model.PartnershipEvent
import com.devwithguru.cricket.domain.model.WicketEvent

// --- BatterState ---
fun BatterStatsEntity.toDomain() = BatterState(
    name = name,
    runs = runs,
    balls = balls,
    fours = fours,
    sixes = sixes,
    isDismissed = isDismissed,
    dismissalType = dismissalType,
    bowlerName = bowlerName,
    fielderName = fielderName
)

fun BatterState.toEntity(inningsId: Long) = BatterStatsEntity(
    inningsId = inningsId,
    name = name,
    runs = runs,
    balls = balls,
    fours = fours,
    sixes = sixes,
    isDismissed = isDismissed,
    dismissalType = dismissalType,
    bowlerName = bowlerName,
    fielderName = fielderName
)

// --- BowlerState ---
fun BowlerStatsEntity.toDomain() = BowlerState(
    name = name,
    balls = balls,
    runsConceded = runsConceded,
    wickets = wickets
)

fun BowlerState.toEntity(inningsId: Long) = BowlerStatsEntity(
    inningsId = inningsId,
    name = name,
    balls = balls,
    runsConceded = runsConceded,
    wickets = wickets
)

// --- WicketEvent ---
fun WicketEventEntity.toDomain() = WicketEvent(
    wicketNumber = wicketNumber,
    batsmanName = batsmanName,
    teamRuns = teamRuns,
    overs = overs
)

fun WicketEvent.toEntity(inningsId: Long) = WicketEventEntity(
    inningsId = inningsId,
    wicketNumber = wicketNumber,
    batsmanName = batsmanName,
    teamRuns = teamRuns,
    overs = overs
)

// --- PartnershipEvent ---
fun PartnershipEventEntity.toDomain() = PartnershipEvent(
    wicketNumber = wicketNumber,
    batter1Name = batter1Name,
    batter2Name = batter2Name,
    runs = runs,
    balls = balls,
    batter1ContributionRuns = batter1ContributionRuns,
    batter1ContributionBalls = batter1ContributionBalls,
    batter2ContributionRuns = batter2ContributionRuns,
    batter2ContributionBalls = batter2ContributionBalls
)

fun PartnershipEvent.toEntity(inningsId: Long) = PartnershipEventEntity(
    inningsId = inningsId,
    wicketNumber = wicketNumber,
    batter1Name = batter1Name,
    batter2Name = batter2Name,
    runs = runs,
    balls = balls,
    batter1ContributionRuns = batter1ContributionRuns,
    batter1ContributionBalls = batter1ContributionBalls,
    batter2ContributionRuns = batter2ContributionRuns,
    batter2ContributionBalls = batter2ContributionBalls
)
