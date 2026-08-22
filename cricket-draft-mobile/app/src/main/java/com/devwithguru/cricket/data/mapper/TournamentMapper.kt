package com.devwithguru.cricket.data.mapper

import com.devwithguru.cricket.data.db.entity.TournamentEntity
import com.devwithguru.cricket.domain.model.Tournament

fun TournamentEntity.toDomain() = Tournament(
    id = id,
    name = name,
    city = city,
    season = season,
    startDate = startDate,
    endDate = endDate,
    ballType = ballType,
    status = status,
    teamCount = teamCount
)

fun Tournament.toEntity() = TournamentEntity(
    id = id,
    name = name,
    city = city,
    season = season,
    startDate = startDate,
    endDate = endDate,
    ballType = ballType,
    status = status,
    teamCount = teamCount
)
