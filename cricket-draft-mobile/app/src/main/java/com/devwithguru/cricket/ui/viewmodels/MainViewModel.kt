package com.devwithguru.cricket.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.devwithguru.cricket.data.repository.FixtureRepository
import com.devwithguru.cricket.domain.model.ScheduledFixture
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

/**
 * ViewModel for MainActivity — handles fixture lookups and updates
 * that happen in navigation callbacks (TossLineup, MatchCenter, etc.)
 */
@HiltViewModel
class MainViewModel @Inject constructor(
    private val fixtureRepository: FixtureRepository
) : ViewModel() {

    private val _currentFixture = MutableStateFlow<ScheduledFixture?>(null)
    val currentFixture: StateFlow<ScheduledFixture?> = _currentFixture

    fun loadFixture(matchId: String) {
        viewModelScope.launch {
            _currentFixture.value = fixtureRepository.getFixtureById(matchId)
        }
    }

    fun getFixture(matchId: String): ScheduledFixture? = _currentFixture.value?.takeIf { it.id == matchId }

    fun updateFixture(fixture: ScheduledFixture) {
        viewModelScope.launch {
            fixtureRepository.updateFixture(fixture)
            _currentFixture.value = fixture
        }
    }
}
