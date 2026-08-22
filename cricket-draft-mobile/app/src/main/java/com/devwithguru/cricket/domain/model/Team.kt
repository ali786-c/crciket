package com.devwithguru.cricket.domain.model

data class Team(
    val id: String,
    val name: String,
    val shortName: String = "",
    val tournamentId: String = "",
    val tournamentName: String = "",
    val playerCount: Int = 0,
    val wins: Int = 0,
    val losses: Int = 0,
    val ties: Int = 0,
    val foundedYear: String = ""
)
