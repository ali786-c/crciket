package com.devwithguru.cricket.data.mapper

import com.devwithguru.cricket.data.db.entity.PlayerEntity
import com.devwithguru.cricket.ui.screens.RegisteredPlayer

fun PlayerEntity.toDomain() = RegisteredPlayer(
    id = id,
    name = name,
    role = role,
    isRegistered = isRegistered
)

fun RegisteredPlayer.toEntity() = PlayerEntity(
    id = id,
    name = name,
    role = role,
    isRegistered = isRegistered
)
