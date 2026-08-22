package com.devwithguru.cricket.data.db.dao

import androidx.room.Dao
import androidx.room.Delete
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import androidx.room.Update
import com.devwithguru.cricket.data.db.entity.TeamEntity
import kotlinx.coroutines.flow.Flow

@Dao
interface TeamDao {
    @Query("SELECT * FROM teams ORDER BY name")
    fun getAllTeams(): Flow<List<TeamEntity>>

    @Query("SELECT * FROM teams WHERE id = :id")
    suspend fun findById(id: String): TeamEntity?

    @Query("SELECT * FROM teams WHERE id = :id")
    fun observeById(id: String): Flow<TeamEntity?>

    @Query("SELECT * FROM teams WHERE tournamentId = :tournamentId ORDER BY name")
    fun getTeamsByTournament(tournamentId: String): Flow<List<TeamEntity>>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertTeam(team: TeamEntity): Long

    @Update
    suspend fun updateTeam(team: TeamEntity)

    @Delete
    suspend fun deleteTeam(team: TeamEntity)
}
