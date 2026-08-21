package com.devwithguru.cricket.data.db.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "fixtures")
data class FixtureEntity(
    @PrimaryKey
    val id: String,
    val homeTeam: String,
    val awayTeam: String,
    val overs: Int,
    val ballType: String,
    val matchType: String,
    val wickets: Int,
    val venue: String,
    val date: String,
    val time: String,
    val status: String = "Scheduled",
    val currentRuns: Int = 0,
    val currentWickets: Int = 0,
    val oversBowled: String = "0.0",
    val strikerName: String = "",
    val nonStrikerName: String = "",
    val bowlerName: String = "",
    val currentInnings: Int = 1,
    val homeSquad: String = "[]",   // JSON serialized List<String>
    val awaySquad: String = "[]"    // JSON serialized List<String>
)
