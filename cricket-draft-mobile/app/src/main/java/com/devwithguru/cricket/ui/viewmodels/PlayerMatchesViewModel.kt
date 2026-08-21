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
class PlayerMatchesViewModel @Inject constructor(
    private val fixtureRepository: FixtureRepository
) : ViewModel() {

    private val _fixtures = MutableStateFlow<List<ScheduledFixture>>(emptyList())
    val fixtures: StateFlow<List<ScheduledFixture>> = _fixtures

    fun loadAllFixtures() {
        viewModelScope.launch {
            fixtureRepository.getAllFixtures().collect {
                _fixtures.value = it
            }
        }
    }

    fun loadFixturesByStatus(status: String) {
        viewModelScope.launch {
            fixtureRepository.getFixturesByStatus(status).collect {
                _fixtures.value = it
            }
        }
    }
}
