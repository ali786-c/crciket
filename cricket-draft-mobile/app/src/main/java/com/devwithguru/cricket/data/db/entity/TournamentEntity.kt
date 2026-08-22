package com.devwithguru.cricket.data.db.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "tournaments")
data class TournamentEntity(
    @PrimaryKey
    val id: String,
    val name: String,
    val city: String,
    val season: String,
    val startDate: String,
    val endDate: String,
    val ballType: String,
    val status: String = "Upcoming",
    val teamCount: Int = 0
)
