package com.devwithguru.cricket.data.db.entity

import androidx.room.Entity
import androidx.room.ForeignKey
import androidx.room.Index
import androidx.room.PrimaryKey

@Entity(
    tableName = "wicket_events",
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
data class WicketEventEntity(
    @PrimaryKey(autoGenerate = true)
    val id: Long = 0,
    val inningsId: Long,
    val wicketNumber: Int,
    val batsmanName: String,
    val teamRuns: Int,
    val overs: String
)
