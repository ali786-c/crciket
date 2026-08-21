package com.devwithguru.cricket.ui.screens

import androidx.compose.runtime.mutableStateListOf

/**
 * Represents a registered player in the app.
 * Every player added to any team automatically gets a profile here.
 */
data class RegisteredPlayer(
    val id: String,
    val name: String,
    val role: String,
    val isRegistered: Boolean = false // true = has an account/login, false = added manually
)

/**
 * Centralized in-memory player registry.
 *
 * - When a player is added to a team lineup, they are also registered here.
 * - Registered players (with accounts) can be searched by ID.
 * - PlayerProfileScreen reads from this registry.
 * - All players get a profile automatically.
 */
object PlayerRegistry {
    val players = mutableStateListOf<RegisteredPlayer>()

    init {
        // Pre-populate with mock registered players (those who have accounts)
        players.addAll(
            listOf(
                RegisteredPlayer("h1", "Ahmed Ali", "Batter", isRegistered = true),
                RegisteredPlayer("h2", "Bilal Butt", "Batter", isRegistered = true),
                RegisteredPlayer("h3", "Salman Ahmed", "Wicketkeeper", isRegistered = true),
                RegisteredPlayer("h4", "Usman Shinwari", "Bowler", isRegistered = true),
                RegisteredPlayer("h5", "Zain Abbas", "Batter", isRegistered = true),
                RegisteredPlayer("h6", "Imran Khan", "All-rounder", isRegistered = true),
                RegisteredPlayer("h7", "Farhan Saeed", "Bowler", isRegistered = true),
                RegisteredPlayer("h8", "Riaz Afridi", "Bowler", isRegistered = true),
                RegisteredPlayer("h9", "Asif Iqbal", "Batter", isRegistered = true),
                RegisteredPlayer("h10", "Shoaib Malik", "All-rounder", isRegistered = true),
                RegisteredPlayer("h11", "Wahab Riaz", "Bowler", isRegistered = true),
                RegisteredPlayer("a1", "Yasir Khan", "Bowler", isRegistered = true),
                RegisteredPlayer("a2", "Babar Azam", "Batter", isRegistered = true),
                RegisteredPlayer("a3", "Mohammad Rizwan", "Wicketkeeper", isRegistered = true),
                RegisteredPlayer("a4", "Shaheen Afridi", "Bowler", isRegistered = true),
                RegisteredPlayer("a5", "Shadab Khan", "All-rounder", isRegistered = true),
                RegisteredPlayer("a6", "Fakhar Zaman", "Batter", isRegistered = true),
                RegisteredPlayer("a7", "Haris Rauf", "Bowler", isRegistered = true),
                RegisteredPlayer("a8", "Naseem Shah", "Bowler", isRegistered = true),
                RegisteredPlayer("a9", "Iftikhar Ahmed", "All-rounder", isRegistered = true),
                RegisteredPlayer("a10", "Saim Ayub", "Batter", isRegistered = true),
                RegisteredPlayer("a11", "Imad Wasim", "All-rounder", isRegistered = true)
            )
        )
    }

    /** Find a registered player by their ID. Returns null if not found. */
    fun findById(id: String): RegisteredPlayer? {
        return players.find { it.id.equals(id, ignoreCase = true) }
    }

    /** Register a new player (auto-creates profile). Returns the new player's ID. */
    fun registerPlayer(name: String, role: String, isRegistered: Boolean = false): RegisteredPlayer {
        val newId = "p${players.size + 1}"
        val newPlayer = RegisteredPlayer(id = newId, name = name, role = role, isRegistered = isRegistered)
        players.add(newPlayer)
        return newPlayer
    }

    /** Check if a player ID already exists in the registry. */
    fun exists(id: String): Boolean {
        return players.any { it.id.equals(id, ignoreCase = true) }
    }
}
