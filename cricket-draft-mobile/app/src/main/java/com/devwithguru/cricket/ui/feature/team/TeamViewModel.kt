package com.devwithguru.cricket.ui.feature.team

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.devwithguru.cricket.data.api.SquadPlayerData
import com.devwithguru.cricket.data.repository.TeamApiRepository
import com.devwithguru.cricket.data.repository.TeamRepository
import com.devwithguru.cricket.domain.model.Team
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class TeamViewModel @Inject constructor(
    private val teamRepository: TeamRepository,
    private val teamApiRepository: TeamApiRepository
) : ViewModel() {

    private val _teams = MutableStateFlow<List<Team>>(emptyList())
    val teams: StateFlow<List<Team>> = _teams

    private val _currentTeam = MutableStateFlow<Team?>(null)
    val currentTeam: StateFlow<Team?> = _currentTeam

    private val _squad = MutableStateFlow<List<SquadPlayerData>>(emptyList())
    val squad: StateFlow<List<SquadPlayerData>> = _squad

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error

    /**
     * Load all teams from Room.
     */
    fun loadAllTeams() {
        viewModelScope.launch {
            teamRepository.getAllTeams().collect {
                _teams.value = it
            }
        }
    }

    /**
     * Load teams by tournament from Room.
     */
    fun loadTeamsByTournament(tournamentId: String) {
        viewModelScope.launch {
            teamRepository.getTeamsByTournament(tournamentId).collect {
                _teams.value = it
            }
        }
    }

    /**
     * Load team details + squad from API.
     */
    fun loadTeam(id: String) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                // Room fallback
                _currentTeam.value = teamRepository.getTeamById(id)

                // Fetch squad from API
                val result = teamApiRepository.getTeamPlayers(id)
                result.fold(
                    onSuccess = { players ->
                        _squad.value = players
                    },
                    onFailure = { e ->
                        _error.value = e.message
                    }
                )
            } catch (e: Exception) {
                _error.value = e.message
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Save team to Room.
     */
    fun saveTeam(team: Team) {
        viewModelScope.launch {
            teamRepository.saveTeam(team)
            _currentTeam.value = team
        }
    }
}
