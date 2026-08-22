package com.devwithguru.cricket.data.repository

import com.devwithguru.cricket.data.db.dao.BatterStatsDao
import com.devwithguru.cricket.data.db.dao.BowlerStatsDao
import com.devwithguru.cricket.data.db.dao.FixtureDao
import com.devwithguru.cricket.data.db.dao.InningsDao
import com.devwithguru.cricket.data.db.dao.PartnershipEventDao
import com.devwithguru.cricket.data.db.dao.WicketEventDao
import com.devwithguru.cricket.data.db.entity.InningsEntity
import com.devwithguru.cricket.data.mapper.toDomain
import com.devwithguru.cricket.data.mapper.toEntity
import com.devwithguru.cricket.domain.model.ScheduledFixture
import com.devwithguru.cricket.domain.model.BatterState
import com.devwithguru.cricket.domain.model.BowlerState
import com.devwithguru.cricket.domain.model.PartnershipEvent
import com.devwithguru.cricket.domain.model.WicketEvent
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class FixtureRepository @Inject constructor(
    private val fixtureDao: FixtureDao,
    private val inningsDao: InningsDao,
    private val batterStatsDao: BatterStatsDao,
    private val bowlerStatsDao: BowlerStatsDao,
    private val wicketEventDao: WicketEventDao,
    private val partnershipEventDao: PartnershipEventDao
) {
    // --- Fixtures ---

    fun getAllFixtures(): Flow<List<ScheduledFixture>> =
        fixtureDao.getAllFixtures().map { entities ->
            entities.map { it.toDomain() }
        }

    fun getFixturesByStatus(status: String): Flow<List<ScheduledFixture>> =
        fixtureDao.getFixturesByStatus(status).map { entities ->
            entities.map { it.toDomain() }
        }

    suspend fun getFixtureById(id: String): ScheduledFixture? =
        fixtureDao.findById(id)?.toDomain()

    fun observeFixture(id: String): Flow<ScheduledFixture?> =
        fixtureDao.observeById(id).map { it?.toDomain() }

    suspend fun saveFixture(fixture: ScheduledFixture) {
        fixtureDao.insertFixture(fixture.toEntity())
    }

    suspend fun updateFixture(fixture: ScheduledFixture) {
        fixtureDao.updateFixture(fixture.toEntity())
    }

    suspend fun updateStatus(id: String, status: String) {
        fixtureDao.updateStatus(id, status)
    }

    suspend fun deleteFixture(fixture: ScheduledFixture) {
        fixtureDao.deleteFixture(fixture.toEntity())
    }

    // --- Innings ---

    suspend fun getOrCreateInnings(fixtureId: String, inningsNumber: Int): Long {
        val existing = inningsDao.getInnings(fixtureId, inningsNumber)
        return if (existing != null) {
            existing.id
        } else {
            inningsDao.insertInnings(
                InningsEntity(fixtureId = fixtureId, inningsNumber = inningsNumber)
            )
        }
    }

    suspend fun getInningsScoringData(
        fixtureId: String,
        inningsNumber: Int
    ): com.devwithguru.cricket.data.mapper.InningsScoringData? {
        val innings = inningsDao.getInnings(fixtureId, inningsNumber) ?: return null
        val batters = batterStatsDao.getBattersByInningsSync(innings.id)
        val bowlers = bowlerStatsDao.getBowlersByInningsSync(innings.id)
        val fow = wicketEventDao.getWicketsByInningsSync(innings.id)
        val partnerships = partnershipEventDao.getPartnershipsByInningsSync(innings.id)
        return com.devwithguru.cricket.data.mapper.InningsScoringData(
            teamRuns = innings.teamRuns,
            teamWickets = innings.teamWickets,
            totalBalls = innings.totalBalls,
            extras = innings.extras,
            dotBalls = innings.dotBalls,
            batters = batters.map { it.toDomain() },
            bowlers = bowlers.map { it.toDomain() },
            fallOfWickets = fow.map { it.toDomain() },
            partnerships = partnerships.map { it.toDomain() }
        )
    }

    suspend fun saveInningsState(
        fixtureId: String,
        inningsNumber: Int,
        runs: Int,
        wickets: Int,
        totalBalls: Int,
        extras: Int,
        dotBalls: Int,
        batters: List<BatterState>,
        bowlers: List<BowlerState>,
        fallOfWickets: List<WicketEvent>,
        partnerships: List<PartnershipEvent>
    ) {
        val inningsId = getOrCreateInnings(fixtureId, inningsNumber)

        // Update innings aggregate
        inningsDao.updateInnings(
            InningsEntity(
                id = inningsId,
                fixtureId = fixtureId,
                inningsNumber = inningsNumber,
                teamRuns = runs,
                teamWickets = wickets,
                totalBalls = totalBalls,
                extras = extras,
                dotBalls = dotBalls
            )
        )

        // Replace batters
        batterStatsDao.deleteAllByInnings(inningsId)
        batterStatsDao.insertAll(batters.map { it.toEntity(inningsId) })

        // Replace bowlers
        bowlerStatsDao.deleteAllByInnings(inningsId)
        bowlerStatsDao.insertAll(bowlers.map { it.toEntity(inningsId) })

        // Replace fall of wickets
        wicketEventDao.deleteAllByInnings(inningsId)
        wicketEventDao.insertAll(fallOfWickets.map { it.toEntity(inningsId) })

        // Replace partnerships
        partnershipEventDao.deleteAllByInnings(inningsId)
        partnershipEventDao.insertAll(partnerships.map { it.toEntity(inningsId) })
    }
}
