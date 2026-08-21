package com.devwithguru.cricket.data.db.entity

import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey

@Entity(
    tableName = "partnership_events",
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
data class PartnershipEventEntity(
    @PrimaryKey(autoGenerate = true)
    val id: Long = 0,
    val inningsId: Long,
    val wicketNumber: Int,
    val batter1Name: String,
    val batter2Name: String,
    val runs: Int,
    val balls: Int,
    val batter1ContributionRuns: Int = 0,
    val batter1ContributionBalls: Int = 0,
    val batter2ContributionRuns: Int = 0,
    val batter2ContributionBalls: Int = 0
)
