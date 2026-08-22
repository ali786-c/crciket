package com.devwithguru.cricket.data.mapper

import com.devwithguru.cricket.data.db.entity.BatterStatsEntity
import com.devwithguru.cricket.data.db.entity.BowlerStatsEntity
import com.devwithguru.cricket.data.db.entity.FixtureEntity
import com.devwithguru.cricket.data.db.entity.InningsEntity
import com.devwithguru.cricket.data.db.entity.PartnershipEventEntity
import com.devwithguru.cricket.data.db.entity.WicketEventEntity
import com.devwithguru.cricket.domain.model.ScheduledFixture
import com.devwithguru.cricket.domain.model.BatterState
import com.devwithguru.cricket.domain.model.BowlerState
import com.devwithguru.cricket.domain.model.PartnershipEvent
import com.devwithguru.cricket.domain.model.WicketEvent
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
    awaySquad = parseStringList(awaySquad),
    firstInningsRuns = firstInningsRuns,
    firstInningsWickets = firstInningsWickets,
    firstInningsBatsmen = parseBatterStateList(firstInningsBatsmen),
    firstInningsBowlers = parseBowlerStateList(firstInningsBowlers),
    secondInningsBatsmen = parseBatterStateList(secondInningsBatsmen),
    secondInningsBowlers = parseBowlerStateList(secondInningsBowlers),
    firstInningsFOW = parseWicketEventList(firstInningsFOW),
    firstInningsPartnerships = parsePartnershipEventList(firstInningsPartnerships),
    secondInningsFOW = parseWicketEventList(secondInningsFOW),
    secondInningsPartnerships = parsePartnershipEventList(secondInningsPartnerships),
    activePartnershipRuns = activePartnershipRuns,
    activePartnershipBalls = activePartnershipBalls,
    firstInningsExtras = firstInningsExtras,
    secondInningsExtras = secondInningsExtras,
    firstInningsDotBalls = firstInningsDotBalls,
    secondInningsDotBalls = secondInningsDotBalls
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
    awaySquad = toJsonString(awaySquad),
    firstInningsRuns = firstInningsRuns,
    firstInningsWickets = firstInningsWickets,
    firstInningsBatsmen = toJsonBatterStateList(firstInningsBatsmen),
    firstInningsBowlers = toJsonBowlerStateList(firstInningsBowlers),
    secondInningsBatsmen = toJsonBatterStateList(secondInningsBatsmen),
    secondInningsBowlers = toJsonBowlerStateList(secondInningsBowlers),
    firstInningsFOW = toJsonWicketEventList(firstInningsFOW),
    firstInningsPartnerships = toJsonPartnershipEventList(firstInningsPartnerships),
    secondInningsFOW = toJsonWicketEventList(secondInningsFOW),
    secondInningsPartnerships = toJsonPartnershipEventList(secondInningsPartnerships),
    activePartnershipRuns = activePartnershipRuns,
    activePartnershipBalls = activePartnershipBalls,
    firstInningsExtras = firstInningsExtras,
    secondInningsExtras = secondInningsExtras,
    firstInningsDotBalls = firstInningsDotBalls,
    secondInningsDotBalls = secondInningsDotBalls
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
    val batters: List<BatterState>,
    val bowlers: List<BowlerState>,
    val fallOfWickets: List<WicketEvent>,
    val partnerships: List<PartnershipEvent>
)

// --- Helpers ---

private fun parseStringList(json: String): List<String> {
    if (json.isBlank() || json == "null") return emptyList()
    val type = object : TypeToken<List<String>>() {}.type
    return gson.fromJson(json, type) ?: emptyList()
}

private fun toJsonString(list: List<String>): String = gson.toJson(list)

private fun parseBatterStateList(json: String): List<BatterState> {
    if (json.isBlank() || json == "null") return emptyList()
    val type = object : TypeToken<List<BatterState>>() {}.type
    return gson.fromJson(json, type) ?: emptyList()
}

private fun toJsonBatterStateList(list: List<BatterState>): String = gson.toJson(list)

private fun parseBowlerStateList(json: String): List<BowlerState> {
    if (json.isBlank() || json == "null") return emptyList()
    val type = object : TypeToken<List<BowlerState>>() {}.type
    return gson.fromJson(json, type) ?: emptyList()
}

private fun toJsonBowlerStateList(list: List<BowlerState>): String = gson.toJson(list)

private fun parseWicketEventList(json: String): List<WicketEvent> {
    if (json.isBlank() || json == "null") return emptyList()
    val type = object : TypeToken<List<WicketEvent>>() {}.type
    return gson.fromJson(json, type) ?: emptyList()
}

private fun toJsonWicketEventList(list: List<WicketEvent>): String = gson.toJson(list)

private fun parsePartnershipEventList(json: String): List<PartnershipEvent> {
    if (json.isBlank() || json == "null") return emptyList()
    val type = object : TypeToken<List<PartnershipEvent>>() {}.type
    return gson.fromJson(json, type) ?: emptyList()
}

private fun toJsonPartnershipEventList(list: List<PartnershipEvent>): String = gson.toJson(list)
