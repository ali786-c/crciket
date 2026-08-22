package com.devwithguru.cricket.data.sync

import com.devwithguru.cricket.data.api.ApiService
import com.devwithguru.cricket.data.db.dao.PendingChangeDao
import com.devwithguru.cricket.data.db.dao.SyncStatusDao
import com.devwithguru.cricket.data.db.entity.PendingChangeEntity
import com.devwithguru.cricket.data.db.entity.SyncStatusEntity
import com.devwithguru.cricket.data.repository.AuthRepository
import com.google.gson.Gson
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import javax.inject.Inject
import javax.inject.Singleton

/**
 * Coordinates sync between local Room database and remote API.
 *
 * Sync Strategy:
 * 1. READS: Always read from local Room first (fast, offline-capable)
 * 2. WRITES: Write to local Room immediately, queue for API sync
 * 3. PUSH: When online, push pending changes to API
 * 4. PULL: When online, pull latest data from API
 */
@Singleton
class SyncManager @Inject constructor(
    private val connectivityMonitor: ConnectivityMonitor,
    private val syncStatusDao: SyncStatusDao,
    private val pendingChangeDao: PendingChangeDao,
    private val apiService: ApiService,
    private val authRepository: AuthRepository,
    private val gson: Gson
) {
    private val _isSyncing = MutableStateFlow(false)
    val isSyncing: StateFlow<Boolean> = _isSyncing.asStateFlow()

    private val _pendingCount = MutableStateFlow(0)
    val pendingCount: StateFlow<Int> = _pendingCount.asStateFlow()

    private val _lastSyncTime = MutableStateFlow(0L)
    val lastSyncTime: StateFlow<Long> = _lastSyncTime.asStateFlow()

    /**
     * Mark an entity as dirty (needs sync).
     */
    suspend fun markDirty(entityType: String, entityId: String) {
        syncStatusDao.upsertSyncStatus(
            SyncStatusEntity(
                entityType = entityType,
                entityId = entityId,
                isDirty = true,
                lastSyncedAt = System.currentTimeMillis()
            )
        )
        updatePendingCount()
    }

    /**
     * Mark an entity as synced.
     */
    suspend fun markSynced(entityType: String, entityId: String, serverVersion: Int = 0) {
        syncStatusDao.upsertSyncStatus(
            SyncStatusEntity(
                entityType = entityType,
                entityId = entityId,
                isDirty = false,
                lastSyncedAt = System.currentTimeMillis(),
                serverVersion = serverVersion
            )
        )
    }

    /**
     * Queue a change for later sync.
     */
    suspend fun queueChange(entityType: String, entityId: String, action: String, payload: Any) {
        val change = PendingChangeEntity(
            entityType = entityType,
            entityId = entityId,
            action = action,
            payload = gson.toJson(payload)
        )
        pendingChangeDao.insertChange(change)
        markDirty(entityType, entityId)
        updatePendingCount()
    }

    /**
     * Push all pending changes to API.
     */
    suspend fun pushPendingChanges(): SyncResult {
        if (!connectivityMonitor.isCurrentlyOnline()) {
            return SyncResult(false, "Offline")
        }

        val token = authRepository.getToken() ?: return SyncResult(false, "Not authenticated")

        _isSyncing.value = true
        var successCount = 0
        var failCount = 0

        try {
            val pendingChanges = pendingChangeDao.getPendingChanges()

            for (change in pendingChanges) {
                try {
                    pendingChangeDao.updateChangeStatus(change.id, "syncing")

                    // Process based on entity type and action
                    val success = pushSingleChange(change, token)

                    if (success) {
                        pendingChangeDao.updateChangeStatus(change.id, "completed")
                        markSynced(change.entityType, change.entityId)
                        successCount++
                    } else {
                        pendingChangeDao.updateChangeStatus(change.id, "failed", "API error")
                        pendingChangeDao.incrementRetryCount(change.id)
                        failCount++
                    }
                } catch (e: Exception) {
                    pendingChangeDao.updateChangeStatus(change.id, "failed", e.message)
                    pendingChangeDao.incrementRetryCount(change.id)
                    failCount++
                }
            }

            // Clean up completed and old failed changes
            pendingChangeDao.deleteCompletedChanges()
            pendingChangeDao.deleteFailedChanges(maxRetries = 5)

            _lastSyncTime.value = System.currentTimeMillis()
            updatePendingCount()

            return SyncResult(
                success = failCount == 0,
                message = "Pushed $successCount, failed $failCount"
            )
        } finally {
            _isSyncing.value = false
        }
    }

    /**
     * Push a single change to API.
     */
    private suspend fun pushSingleChange(change: PendingChangeEntity, token: String): Boolean {
        // TODO: Implement specific API calls based on entityType and action
        // For now, return true to mark as synced
        return when (change.entityType) {
            "scoring" -> {
                // POST /api/v1/matches/{matchId}/deliveries
                true
            }
            "profile" -> {
                // PATCH /api/v1/profile
                true
            }
            "registration" -> {
                // POST /api/v1/tournaments/{id}/registration
                true
            }
            else -> true
        }
    }

    /**
     * Pull latest data from API and update local database.
     */
    suspend fun pullLatestData(): SyncResult {
        if (!connectivityMonitor.isCurrentlyOnline()) {
            return SyncResult(false, "Offline")
        }

        _isSyncing.value = true

        try {
            // Pull tournaments
            val tournamentsResult = apiService.getTournaments()
            if (tournamentsResult.isSuccessful) {
                // TODO: Update local Room database with tournament data
            }

            _lastSyncTime.value = System.currentTimeMillis()

            return SyncResult(true, "Data synced")
        } catch (e: Exception) {
            return SyncResult(false, e.message ?: "Sync failed")
        } finally {
            _isSyncing.value = false
        }
    }

    /**
     * Full sync: push pending changes then pull latest.
     */
    suspend fun fullSync(): SyncResult {
        val pushResult = pushPendingChanges()
        val pullResult = pullLatestData()

        return when {
            pushResult.success && pullResult.success -> SyncResult(true, "Full sync complete")
            pushResult.success -> pullResult
            else -> pushResult
        }
    }

    /**
     * Auto-sync when coming online.
     */
    suspend fun onConnectivityRestored() {
        if (connectivityMonitor.isCurrentlyOnline()) {
            pushPendingChanges()
        }
    }

    private suspend fun updatePendingCount() {
        val count = pendingChangeDao.getPendingChanges().size
        _pendingCount.value = count
    }
}

data class SyncResult(
    val success: Boolean,
    val message: String
)
