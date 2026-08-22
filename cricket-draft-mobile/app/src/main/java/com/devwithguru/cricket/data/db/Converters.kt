package com.devwithguru.cricket.data.db

import androidx.room.TypeConverter
import com.devwithguru.cricket.domain.model.BatterState
import com.devwithguru.cricket.domain.model.BowlerState
import com.devwithguru.cricket.domain.model.PartnershipEvent
import com.devwithguru.cricket.domain.model.WicketEvent
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken

class Converters {
    private val gson = Gson()

    @TypeConverter
    fun fromStringList(value: List<String>): String = gson.toJson(value)

    @TypeConverter
    fun toStringList(value: String): List<String> {
        if (value.isBlank() || value == "null") return emptyList()
        val type = object : TypeToken<List<String>>() {}.type
        return gson.fromJson(value, type)
    }

    @TypeConverter
    fun fromBatterStateList(value: List<BatterState>): String = gson.toJson(value)

    @TypeConverter
    fun toBatterStateList(value: String): List<BatterState> {
        if (value.isBlank() || value == "null") return emptyList()
        val type = object : TypeToken<List<BatterState>>() {}.type
        return gson.fromJson(value, type) ?: emptyList()
    }

    @TypeConverter
    fun fromBowlerStateList(value: List<BowlerState>): String = gson.toJson(value)

    @TypeConverter
    fun toBowlerStateList(value: String): List<BowlerState> {
        if (value.isBlank() || value == "null") return emptyList()
        val type = object : TypeToken<List<BowlerState>>() {}.type
        return gson.fromJson(value, type) ?: emptyList()
    }

    @TypeConverter
    fun fromWicketEventList(value: List<WicketEvent>): String = gson.toJson(value)

    @TypeConverter
    fun toWicketEventList(value: String): List<WicketEvent> {
        if (value.isBlank() || value == "null") return emptyList()
        val type = object : TypeToken<List<WicketEvent>>() {}.type
        return gson.fromJson(value, type) ?: emptyList()
    }

    @TypeConverter
    fun fromPartnershipEventList(value: List<PartnershipEvent>): String = gson.toJson(value)

    @TypeConverter
    fun toPartnershipEventList(value: String): List<PartnershipEvent> {
        if (value.isBlank() || value == "null") return emptyList()
        val type = object : TypeToken<List<PartnershipEvent>>() {}.type
        return gson.fromJson(value, type) ?: emptyList()
    }
}
