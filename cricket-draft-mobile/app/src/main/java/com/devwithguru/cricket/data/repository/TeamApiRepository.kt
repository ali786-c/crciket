package com.devwithguru.cricket.data.repository

import com.devwithguru.cricket.data.api.ApiService
import com.devwithguru.cricket.data.api.TeamSquadData
import com.devwithguru.cricket.data.api.SquadPlayerData
import javax.inject.Inject
import javax.inject.Singleton

/**
 * Repository for team-related API calls.
 */
@Singleton
class TeamApiRepository @Inject constructor(
    private val apiService: ApiService
) {
    /**
     * Get team squad.
     */
    suspend fun getTeamSquad(teamId: String): Result<TeamSquadData> {
        return try {
            val response = apiService.getTeamSquad(teamId)
            if (response.isSuccessful) {
                val data = response.body()?.data
                if (data != null) Result.success(data)
                else Result.failure(Exception("No data"))
            } else {
                Result.failure(Exception("Failed to load team squad"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    /**
     * Get team squad as a simple player list.
     */
    suspend fun getTeamPlayers(teamId: String): Result<List<SquadPlayerData>> {
        return try {
            val result = getTeamSquad(teamId)
            result.fold(
                onSuccess = { squad -> Result.success(squad.squad) },
                onFailure = { e -> Result.failure(e) }
            )
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
