package com.devwithguru.cricket.ui.feature.match.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.devwithguru.cricket.data.repository.FixtureRepository
import com.devwithguru.cricket.domain.model.ScheduledFixture
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class CreateMatchViewModel @Inject constructor(
    private val fixtureRepository: FixtureRepository
) : ViewModel() {

    private val _saveSuccess = MutableStateFlow<String?>(null)
    val saveSuccess: StateFlow<String?> = _saveSuccess

    fun saveFixture(fixture: ScheduledFixture) {
        viewModelScope.launch {
            fixtureRepository.saveFixture(fixture)
            _saveSuccess.value = fixture.id
        }
    }

    fun clearSaveSuccess() {
        _saveSuccess.value = null
    }
}
