package com.devwithguru.cricket.ui.feature.auth

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.devwithguru.cricket.data.api.UpdateProfileRequest
import com.devwithguru.cricket.data.repository.AuthRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

data class OnboardingUiState(
    val isLoading: Boolean = false,
    val isCompleted: Boolean = false,
    val error: String? = null
)

@HiltViewModel
class PlayerOnboardingViewModel @Inject constructor(
    private val authRepository: AuthRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(OnboardingUiState())
    val uiState: StateFlow<OnboardingUiState> = _uiState.asStateFlow()

    /**
     * Submit player profile to API.
     */
    fun submitProfile(
        fullName: String,
        role: String,
        battingStyle: String,
        bowlingStyle: String,
        city: String,
        bio: String
    ) {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, error = null)

            val request = UpdateProfileRequest(
                full_name = fullName,
                playing_role = role,
                batting_style = battingStyle,
                bowling_style = if (bowlingStyle == "None") null else bowlingStyle,
                city = city.ifBlank { null },
                bio = bio.ifBlank { null }
            )

            val result = authRepository.updateProfile(request)

            result.fold(
                onSuccess = {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        isCompleted = true,
                        error = null
                    )
                },
                onFailure = { e ->
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        error = e.message ?: "Failed to save profile"
                    )
                }
            )
        }
    }

    fun clearError() {
        _uiState.value = _uiState.value.copy(error = null)
    }
}
