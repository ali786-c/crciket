package com.devwithguru.cricket.data.db.entity

import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey

@Entity(
    tableName = "batter_stats",
    foreignKeys = [
        ForeignKey(
            entity = InningsEntity::class,
            parentColumns = ["id"],
            childColumns = ["inningsId"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [Index(value = ["inningsId"])]
)
data class BatterStatsEntity(
    @PrimaryKey(autoGenerate = true)
    val id: Long = 0,
    val inningsId: Long,
    val name: String,
    val runs: Int = 0,
    val balls: Int = 0,
    val fours: Int = 0,
    val sixes: Int = 0,
    val isDismissed: Boolean = false,
    val dismissalType: String? = null,
    val bowlerName: String? = null,
    val fielderName: String? = null
)
