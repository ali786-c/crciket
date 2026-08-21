package com.devwithguru.cricket.data.db.entity

import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey

@Entity(
    tableName = "innings",
    foreignKeys = [
        ForeignKey(
            entity = FixtureEntity::class,
            parentColumns = ["id"],
            childColumns = ["fixtureId"],
            onDelete = ForeignKey.CASCADE
        )
    ],
    indices = [Index(value = ["fixtureId"])]
)
data class InningsEntity(
    @PrimaryKey(autoGenerate = true)
    val id: Long = 0,
    val fixtureId: String,
    val inningsNumber: Int,  // 1 or 2
    val teamRuns: Int = 0,
    val teamWickets: Int = 0,
    val totalBalls: Int = 0,
    val extras: Int = 0,
    val dotBalls: Int = 0
)
