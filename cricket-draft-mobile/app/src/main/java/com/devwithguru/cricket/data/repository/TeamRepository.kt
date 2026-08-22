package com.devwithguru.cricket.data.repository

import com.devwithguru.cricket.data.db.dao.TeamDao
import com.devwithguru.cricket.data.mapper.toDomain
import com.devwithguru.cricket.data.mapper.toEntity
import com.devwithguru.cricket.domain.model.Team
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class TeamRepository @Inject constructor(
    private val teamDao: TeamDao
) {
    fun getAllTeams(): Flow<List<Team>> =
        teamDao.getAllTeams().map { entities ->
            entities.map { it.toDomain() }
        }

    fun getTeamsByTournament(tournamentId: String): Flow<List<Team>> =
        teamDao.getTeamsByTournament(tournamentId).map { entities ->
            entities.map { it.toDomain() }
        }

    suspend fun getTeamById(id: String): Team? =
        teamDao.findById(id)?.toDomain()

    fun observeTeam(id: String): Flow<Team?> =
        teamDao.observeById(id).map { it?.toDomain() }

    suspend fun saveTeam(team: Team) {
        teamDao.insertTeam(team.toEntity())
    }

    suspend fun updateTeam(team: Team) {
        teamDao.updateTeam(team.toEntity())
    }

    suspend fun deleteTeam(team: Team) {
        teamDao.deleteTeam(team.toEntity())
    }
}
