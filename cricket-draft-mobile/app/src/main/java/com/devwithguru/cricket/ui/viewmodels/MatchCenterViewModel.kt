package com.devwithguru.cricket.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.devwithguru.cricket.data.repository.FixtureRepository
import com.devwithguru.cricket.ui.screens.ScheduledFixture
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class MatchCenterViewModel @Inject constructor(
    private val fixtureRepository: FixtureRepository
) : ViewModel() {

    private val _fixture = MutableStateFlow<ScheduledFixture?>(null)
    val fixture: StateFlow<ScheduledFixture?> = _fixture

    fun loadFixture(matchId: String) {
        viewModelScope.launch {
            _fixture.value = fixtureRepository.getFixtureById(matchId)
        }
    }

    fun observeFixture(matchId: String): StateFlow<ScheduledFixture?> {
        viewModelScope.launch {
            fixtureRepository.observeFixture(matchId).collect {
                _fixture.value = it
            }
        }
        return _fixture
    }

    fun updateFixtureStatus(matchId: String, status: String) {
        viewModelScope.launch {
            fixtureRepository.updateStatus(matchId, status)
            _fixture.value = fixtureRepository.getFixtureById(matchId)
        }
    }
}
