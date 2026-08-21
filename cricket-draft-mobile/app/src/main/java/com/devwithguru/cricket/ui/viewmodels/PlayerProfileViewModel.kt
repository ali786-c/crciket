package com.devwithguru.cricket.ui.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.devwithguru.cricket.data.repository.PlayerRepository
import com.devwithguru.cricket.ui.screens.RegisteredPlayer
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class PlayerProfileViewModel @Inject constructor(
    private val playerRepository: PlayerRepository
) : ViewModel() {

    private val _player = MutableStateFlow<RegisteredPlayer?>(null)
    val player: StateFlow<RegisteredPlayer?> = _player

    fun loadPlayer(playerId: String) {
        viewModelScope.launch {
            _player.value = playerRepository.findById(playerId)
        }
    }
}
