package com.devwithguru.cricket.data.db.entity

import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey

@Entity(
    tableName = "bowler_stats",
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
data class BowlerStatsEntity(
    @PrimaryKey(autoGenerate = true)
    val id: Long = 0,
    val inningsId: Long,
    val name: String,
    val balls: Int = 0,
    val runsConceded: Int = 0,
    val wickets: Int = 0
)
