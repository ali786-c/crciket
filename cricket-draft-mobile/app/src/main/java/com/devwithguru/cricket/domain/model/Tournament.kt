package com.devwithguru.cricket.domain.model

data class Tournament(
    val id: String,
    val name: String,
    val city: String,
    val season: String,
    val startDate: String,
    val endDate: String,
    val ballType: String,
    val status: String = "Upcoming", // "Upcoming", "Active", "Completed"
    val teamCount: Int = 0
)
