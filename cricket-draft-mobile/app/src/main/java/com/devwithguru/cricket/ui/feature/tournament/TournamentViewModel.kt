package com.devwithguru.cricket.ui.feature.tournament

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.devwithguru.cricket.data.api.TournamentData
import com.devwithguru.cricket.data.api.TournamentTeamData
import com.devwithguru.cricket.data.api.TournamentStandingData
import com.devwithguru.cricket.data.api.TournamentFixtureData2
import com.devwithguru.cricket.data.repository.TournamentApiRepository
import com.devwithguru.cricket.data.repository.TournamentRepository
import com.devwithguru.cricket.domain.model.Tournament
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class TournamentViewModel @Inject constructor(
    private val tournamentRepository: TournamentRepository,
    private val tournamentApiRepository: TournamentApiRepository
) : ViewModel() {

    private val _tournaments = MutableStateFlow<List<Tournament>>(emptyList())
    val tournaments: StateFlow<List<Tournament>> = _tournaments

    private val _currentTournament = MutableStateFlow<Tournament?>(null)
    val currentTournament: StateFlow<Tournament?> = _currentTournament

    private val _isLoading = MutableStateFlow(false)
    val isLoading: StateFlow<Boolean> = _isLoading

    private val _error = MutableStateFlow<String?>(null)
    val error: StateFlow<String?> = _error

    // ─── Tournament Detail Data ──────────────────────────────

    private val _teams = MutableStateFlow<List<TournamentTeamData>>(emptyList())
    val teams: StateFlow<List<TournamentTeamData>> = _teams

    private val _standings = MutableStateFlow<List<TournamentStandingData>>(emptyList())
    val standings: StateFlow<List<TournamentStandingData>> = _standings

    private val _fixtures = MutableStateFlow<List<TournamentFixtureData2>>(emptyList())
    val fixtures: StateFlow<List<TournamentFixtureData2>> = _fixtures

    /**
     * Load all tournaments from API.
     */
    fun loadAllTournaments() {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                val result = tournamentApiRepository.getTournaments()
                result.fold(
                    onSuccess = { apiTournaments ->
                        _tournaments.value = apiTournaments.map { it.toDomain() }
                    },
                    onFailure = { e ->
                        _error.value = e.message
                        // Fallback to Room
                        tournamentRepository.getAllTournaments().collect {
                            _tournaments.value = it
                        }
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
     * Load tournament details + teams + standings + fixtures from API.
     */
    fun loadTournament(id: String) {
        viewModelScope.launch {
            _isLoading.value = true
            try {
                // Load tournament details
                val tournamentResult = tournamentApiRepository.getTournament(id)
                tournamentResult.fold(
                    onSuccess = { data ->
                        _currentTournament.value = data.toDomain()
                    },
                    onFailure = { e ->
                        _error.value = e.message
                        // Fallback to Room
                        _currentTournament.value = tournamentRepository.getTournamentById(id)
                    }
                )

                // Load teams
                val teamsResult = tournamentApiRepository.getTournamentTeams(id)
                teamsResult.onSuccess { _teams.value = it }

                // Load standings
                val standingsResult = tournamentApiRepository.getTournamentStandings(id)
                standingsResult.onSuccess { _standings.value = it }

                // Load fixtures
                val fixturesResult = tournamentApiRepository.getTournamentFixtures(id)
                fixturesResult.onSuccess { _fixtures.value = it }

            } catch (e: Exception) {
                _error.value = e.message
            } finally {
                _isLoading.value = false
            }
        }
    }

    /**
     * Save tournament to Room.
     */
    fun saveTournament(tournament: Tournament) {
        viewModelScope.launch {
            tournamentRepository.saveTournament(tournament)
            _currentTournament.value = tournament
        }
    }

    /**
     * Clear error.
     */
    fun clearError() {
        _error.value = null
    }
}

// ─── Extension: API → Domain ──────────────────────────────────

fun TournamentData.toDomain() = Tournament(
    id = id.toString(),
    name = name ?: "Unknown",
    city = city ?: "",
    season = season_name ?: "",
    startDate = starts_on ?: "",
    endDate = ends_on ?: "",
    ballType = rule_profile?.format ?: "T20",
    status = status ?: "Upcoming"
)
