package com.devwithguru.cricket.data.db.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

/**
 * Tracks when data was last synced with server.
 */
@Entity(tableName = "sync_status")
data class SyncStatusEntity(
    @PrimaryKey
    val entityType: String,  // "tournament", "fixture", "player", "team", "match"
    val entityId: String,
    val lastSyncedAt: Long = System.currentTimeMillis(),
    val serverVersion: Int = 0,
    val isDirty: Boolean = false  // true if local changes need to be pushed
)

/**
 * Queue of local changes that need to be pushed to server.
 */
@Entity(tableName = "pending_changes")
data class PendingChangeEntity(
    @PrimaryKey(autoGenerate = true)
    val id: Long = 0,
    val entityType: String,      // "scoring", "profile", "registration"
    val entityId: String,
    val action: String,          // "create", "update", "delete"
    val payload: String,         // JSON payload
    val createdAt: Long = System.currentTimeMillis(),
    val retryCount: Int = 0,
    val lastError: String? = null,
    val status: String = "pending"  // "pending", "syncing", "failed", "completed"
)
