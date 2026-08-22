package com.devwithguru.cricket.ui.feature.player

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.devwithguru.cricket.data.api.PlayerStatsDetailData
import com.devwithguru.cricket.data.api.PlayerInsightsDetailData
import com.devwithguru.cricket.data.api.PlayerMatchData
import com.devwithguru.cricket.data.api.PlayerTeamData
import com.devwithguru.cricket.data.repository.AuthRepository
import com.devwithguru.cricket.data.repository.PlayerApiRepository
import com.devwithguru.cricket.data.repository.PlayerRepository
import com.devwithguru.cricket.domain.model.RegisteredPlayer
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class PlayerProfileViewModel @Inject constructor(
    private val playerRepository: PlayerRepository,
    private val playerApiRepository: PlayerApiRepository,
    private val authRepository: AuthRepository
) : ViewModel() {

    private val _player = MutableStateFlow<RegisteredPlayer?>(null)
    val player: StateFlow<RegisteredPlayer?> = _player

    private val _stats = MutableStateFlow<PlayerStatsDetailData?>(null)
    val stats: StateFlow<PlayerStatsDetailData?> = _stats

    private val _insights = MutableStateFlow<PlayerInsightsDetailData?>(null)
    val insights: StateFlow<PlayerInsightsDetailData?> = _insights

    private val _matches = MutableStateFlow<List<PlayerMatchData>>(emptyList())
    val matches: StateFlow<List<PlayerMatchData>> = _matches

    private val _teams = MutableStateFlow<List<PlayerTeamData>>(emptyList())
    val teams: StateFlow<List<PlayerTeamData>> = _teams

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error

    /**
     * Load player profile from API (or Room fallback).
     */
    fun loadPlayer(playerId: String) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                // Try Room first
                _player.value = playerRepository.findById(playerId)

                // Fetch from API
                val token = authRepository.getRawToken()?.let { "Bearer $it" }

                val statsResult = playerApiRepository.getPlayerStats(playerId, token)
                statsResult.onSuccess { _stats.value = it }

                val insightsResult = playerApiRepository.getPlayerInsights(playerId, token)
                insightsResult.onSuccess { _insights.value = it }

                val matchesResult = playerApiRepository.getPlayerMatches(playerId, token)
                matchesResult.onSuccess { _matches.value = it }

                val teamsResult = playerApiRepository.getPlayerTeams(playerId, token)
                teamsResult.onSuccess { _teams.value = it }

            } catch (e: Exception) {
                _error.value = e.message
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Load player from Room only.
     */
    fun loadPlayerFromRoom(playerId: String) {
        viewModelScope.launch {
            _player.value = playerRepository.findById(playerId)
        }
    }
}
