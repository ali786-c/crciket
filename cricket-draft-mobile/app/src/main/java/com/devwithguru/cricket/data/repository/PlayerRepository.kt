package com.devwithguru.cricket.data.repository

import com.devwithguru.cricket.data.db.dao.PlayerDao
import com.devwithguru.cricket.data.mapper.toDomain
import com.devwithguru.cricket.data.mapper.toEntity
import com.devwithguru.cricket.ui.screens.RegisteredPlayer
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.flow.map
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class PlayerRepository @Inject constructor(
    private val playerDao: PlayerDao
) {
    fun getAllPlayers(): Flow<List<RegisteredPlayer>> =
        playerDao.getAllPlayers().map { entities ->
            entities.map { it.toDomain() }
        }

    suspend fun findById(id: String): RegisteredPlayer? =
        playerDao.findById(id)?.toDomain()

    suspend fun registerPlayer(name: String, role: String, isRegistered: Boolean = false): RegisteredPlayer {
        val currentCount = playerDao.count()
        val newId = "p${currentCount + 1}"
        val entity = com.devwithguru.cricket.data.db.entity.PlayerEntity(
            id = newId,
            name = name,
            role = role,
            isRegistered = isRegistered
        )
        playerDao.insertPlayer(entity)
        return entity.toDomain()
    }

    suspend fun exists(id: String): Boolean = playerDao.exists(id)

    suspend fun getPlayersList(): List<RegisteredPlayer> =
        playerDao.getAllPlayers().first().map { it.toDomain() }
}
