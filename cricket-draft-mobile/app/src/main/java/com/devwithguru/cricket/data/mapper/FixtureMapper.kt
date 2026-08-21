package com.devwithguru.cricket.data.mapper

import com.devwithguru.cricket.data.db.entity.BatterStatsEntity
import com.devwithguru.cricket.data.db.entity.BowlerStatsEntity
import com.devwithguru.cricket.data.db.entity.FixtureEntity
import com.devwithguru.cricket.data.db.entity.InningsEntity
import com.devwithguru.cricket.data.db.entity.PartnershipEventEntity
import com.devwithguru.cricket.data.db.entity.WicketEventEntity
import com.devwithguru.cricket.ui.screens.ScheduledFixture
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken

private val gson = Gson()

// --- FixtureEntity ↔ ScheduledFixture ---

fun FixtureEntity.toDomain() = ScheduledFixture(
    id = id,
    homeTeam = homeTeam,
    awayTeam = awayTeam,
    overs = overs,
    ballType = ballType,
    matchType = matchType,
    wickets = wickets,
    venue = venue,
    date = date,
    time = time,
    status = status,
    currentRuns = currentRuns,
    currentWickets = currentWickets,
    oversBowled = oversBowled,
    strikerName = strikerName,
    nonStrikerName = nonStrikerName,
    bowlerName = bowlerName,
    currentInnings = currentInnings,
    homeSquad = parseStringList(homeSquad),
    awaySquad = parseStringList(awaySquad)
)

fun ScheduledFixture.toEntity() = FixtureEntity(
    id = id,
    homeTeam = homeTeam,
    awayTeam = awayTeam,
    overs = overs,
    ballType = ballType,
    matchType = matchType,
    wickets = wickets,
    venue = venue,
    date = date,
    time = time,
    status = status,
    currentRuns = currentRuns,
    currentWickets = currentWickets,
    oversBowled = oversBowled,
    strikerName = strikerName,
    nonStrikerName = nonStrikerName,
    bowlerName = bowlerName,
    currentInnings = currentInnings,
    homeSquad = toJsonString(homeSquad),
    awaySquad = toJsonString(awaySquad)
)

// --- InningsEntity ---

fun InningsEntity.toScoringData(
    batters: List<BatterStatsEntity>,
    bowlers: List<BowlerStatsEntity>,
    fow: List<WicketEventEntity>,
    partnerships: List<PartnershipEventEntity>
): InningsScoringData = InningsScoringData(
    teamRuns = teamRuns,
    teamWickets = teamWickets,
    totalBalls = totalBalls,
    extras = extras,
    dotBalls = dotBalls,
    batters = batters.map { it.toDomain() },
    bowlers = bowlers.map { it.toDomain() },
    fallOfWickets = fow.map { it.toDomain() },
    partnerships = partnerships.map { it.toDomain() }
)

data class InningsScoringData(
    val teamRuns: Int,
    val teamWickets: Int,
    val totalBalls: Int,
    val extras: Int,
    val dotBalls: Int,
    val batters: List<com.devwithguru.cricket.ui.screens.match.BatterState>,
    val bowlers: List<com.devwithguru.cricket.ui.screens.match.BowlerState>,
    val fallOfWickets: List<com.devwithguru.cricket.ui.screens.match.WicketEvent>,
    val partnerships: List<com.devwithguru.cricket.ui.screens.match.PartnershipEvent>
)

// --- Helpers ---

private fun parseStringList(json: String): List<String> {
    if (json.isBlank() || json == "[]") return emptyList()
    val type = object : TypeToken<List<String>>() {}.type
    return gson.fromJson(json, type)
}

private fun toJsonString(list: List<String>): String = gson.toJson(list)
