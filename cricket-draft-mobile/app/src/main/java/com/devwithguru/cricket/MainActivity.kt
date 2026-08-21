package com.devwithguru.cricket

import android.os.Bundle
import android.widget.Toast
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.compose.BackHandler
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import com.devwithguru.cricket.ui.screens.LoginScreen
import com.devwithguru.cricket.ui.screens.PlayerOnboardingScreen
import com.devwithguru.cricket.ui.screens.UnifiedHomeScreen
import com.devwithguru.cricket.ui.screens.CreateMatchScreen
import com.devwithguru.cricket.ui.screens.CreateTournamentScreen
import com.devwithguru.cricket.ui.screens.MyTournamentsScreen
import com.devwithguru.cricket.ui.screens.TournamentHubScreen
import com.devwithguru.cricket.ui.screens.TeamDetailScreen
import com.devwithguru.cricket.ui.screens.TossLineupScreen
import com.devwithguru.cricket.ui.screens.TossScreen
import com.devwithguru.cricket.ui.screens.match.LiveScorerScreen
import com.devwithguru.cricket.ui.screens.MatchCenterScreen
import com.devwithguru.cricket.ui.screens.PlayerProfileScreen
import com.devwithguru.cricket.ui.screens.GlobalSearchScreen
import com.devwithguru.cricket.ui.screens.MatchEditorScreen
import com.devwithguru.cricket.ui.screens.RecentMatchesScreen
import com.devwithguru.cricket.ui.screens.ScheduledFixture
import com.devwithguru.cricket.ui.screens.FixtureStore
import com.devwithguru.cricket.ui.screens.match.LiveScorerViewModel
import com.devwithguru.cricket.ui.theme.CricketTheme
import com.devwithguru.cricket.ui.navigation.Screen
import com.devwithguru.cricket.ui.viewmodels.NavigationViewModel

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            var isDarkTheme by remember { mutableStateOf(false) }
            CricketTheme(darkTheme = isDarkTheme) {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    val navigationViewModel = remember { NavigationViewModel() }
                    val currentScreen = navigationViewModel.currentScreen
                    val navigationStack = navigationViewModel.navigationStack
                    var loggedInEmail by remember { mutableStateOf("") }
                    val scorerViewModel = remember { LiveScorerViewModel() }
                    val context = LocalContext.current

                    // Intercept system back button clicks dynamically
                    BackHandler(enabled = navigationStack.size > 1 && currentScreen != Screen.Home) {
                        navigationViewModel.navigateBack()
                    }

                    when (val screen = currentScreen) {
                        Screen.Login -> {
                            LoginScreen(
                                onLoginSuccess = { email ->
                                    loggedInEmail = email
                                    navigationViewModel.clearAndNavigateTo(Screen.Home)
                                    Toast.makeText(context, "Logged in as $email", Toast.LENGTH_SHORT).show()
                                },
                                onNavigateToRegister = {
                                    navigationViewModel.navigateTo(Screen.Onboarding)
                                }
                            )
                        }
                        Screen.Onboarding -> {
                            PlayerOnboardingScreen(
                                onSubmitRegistration = { name, role, batting, bowling, city, bio ->
                                    Toast.makeText(context, "Registered $name as $role", Toast.LENGTH_LONG).show()
                                    navigationViewModel.clearAndNavigateTo(Screen.Home)
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen == Screen.Onboarding) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        Screen.Home -> {
                            UnifiedHomeScreen(
                                userName = loggedInEmail.ifBlank { "Ahmed Ali" },
                                isDarkTheme = isDarkTheme,
                                onToggleTheme = { isDarkTheme = it },
                                onNavigateToCreateMatch = {
                                    navigationViewModel.navigateTo(Screen.CreateMatch)
                                },
                                onNavigateToCreateTournament = {
                                    navigationViewModel.navigateTo(Screen.CreateTournament)
                                },
                                onNavigateToMyTournaments = {
                                    navigationViewModel.navigateTo(Screen.MyTournaments)
                                },
                                onNavigateToMyTeams = {
                                    Toast.makeText(context, "Navigate: My Teams", Toast.LENGTH_SHORT).show()
                                },
                                onNavigateToPlayerProfile = {
                                    navigationViewModel.navigateTo(Screen.PlayerProfile("p1"))
                                },
                                onNavigateToTournamentHub = { id ->
                                    navigationViewModel.navigateTo(Screen.TournamentHub(id))
                                },
                                onNavigateToMatchCenter = { id ->
                                    navigationViewModel.navigateTo(Screen.MatchCenter(matchId = id, isScorer = false))
                                },
                                onNavigateToRecentMatches = {
                                    navigationViewModel.navigateTo(Screen.RecentMatches)
                                },
                                onSearchClick = {
                                    navigationViewModel.navigateTo(Screen.GlobalSearch)
                                },
                                onLogout = {
                                    loggedInEmail = ""
                                    navigationViewModel.clearAndNavigateTo(Screen.Login)
                                    Toast.makeText(context, "Logged out successfully", Toast.LENGTH_SHORT).show()
                                }
                            )
                        }
                        Screen.CreateMatch -> {
                            CreateMatchScreen(
                                onCreateMatchSuccess = { matchId, home, away, overs, ballType, date, time ->
                                    navigationViewModel.navigateTo(Screen.Toss(matchId, home, away))
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen == Screen.CreateMatch) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        Screen.CreateTournament -> {
                            CreateTournamentScreen(
                                onCreateTournamentSuccess = { name, city, season, start, end, ballType ->
                                    Toast.makeText(context, "Created Tournament: $name in $city ($season) with $ballType Ball", Toast.LENGTH_LONG).show()
                                    if (navigationViewModel.currentScreen == Screen.CreateTournament) {
                                        navigationViewModel.navigateBack()
                                    }
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen == Screen.CreateTournament) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        Screen.MyTournaments -> {
                            MyTournamentsScreen(
                                onNavigateToCreateTournament = {
                                    navigationViewModel.navigateTo(Screen.CreateTournament)
                                },
                                onNavigateToTournamentHub = { id ->
                                    navigationViewModel.navigateTo(Screen.TournamentHub(id))
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen == Screen.MyTournaments) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        is Screen.TournamentHub -> {
                            TournamentHubScreen(
                                tournamentId = screen.tournamentId,
                                onNavigateToTeamDetail = { teamId ->
                                    navigationViewModel.navigateTo(Screen.TeamDetail(teamId))
                                },
                                onNavigateToMatchCenter = { matchId ->
                                    navigationViewModel.navigateTo(Screen.MatchCenter(matchId = matchId, isScorer = false))
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen is Screen.TournamentHub) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        is Screen.TeamDetail -> {
                            TeamDetailScreen(
                                teamId = screen.teamId,
                                onNavigateToPlayerDetail = { playerId ->
                                    navigationViewModel.navigateTo(Screen.PlayerProfile(playerId))
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen is Screen.TeamDetail) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        is Screen.Toss -> {
                            TossScreen(
                                homeTeamName = screen.homeTeam,
                                awayTeamName = screen.awayTeam,
                                onTossComplete = { winner, decision ->
                                    navigationViewModel.navigateTo(
                                        Screen.TossLineup(
                                            matchId = screen.matchId,
                                            homeTeam = screen.homeTeam,
                                            awayTeam = screen.awayTeam,
                                            tossWinner = winner,
                                            tossDecision = decision
                                        )
                                    )
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen is Screen.Toss) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        is Screen.TossLineup -> {
                            TossLineupScreen(
                                homeTeamName = screen.homeTeam,
                                awayTeamName = screen.awayTeam,
                                tossWinner = screen.tossWinner,
                                tossDecision = screen.tossDecision,
                                onStartMatchSuccess = { winner, decision, homeLineup, awayLineup ->
                                    // Ensure status is marked Live in FixtureStore
                                    FixtureStore.fixtures.find { it.id == screen.matchId }?.let { f ->
                                        f.status = "Live"
                                        f.homeSquad = homeLineup
                                        f.awaySquad = awayLineup
                                    }
                                    Toast.makeText(context, "Match Setup Completed. Starting scorer console.", Toast.LENGTH_SHORT).show()
                                    navigationViewModel.navigateTo(
                                        Screen.MatchCenter(
                                            matchId = screen.matchId,
                                            isScorer = true,
                                            homeSquadList = homeLineup,
                                            awaySquadList = awayLineup
                                        )
                                    )
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen is Screen.TossLineup) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        is Screen.MatchCenter -> {
                            MatchCenterScreen(
                                matchId = screen.matchId,
                                isScorer = screen.isScorer,
                                homeSquadList = screen.homeSquadList,
                                awaySquadList = screen.awaySquadList,
                                scorerViewModel = scorerViewModel,
                                onNavigateBack = {
                                    FixtureStore.fixtures.find { it.id == screen.matchId }?.let { f ->
                                        if (f.status != "Completed") {
                                            f.status = "Live"
                                        }
                                    }
                                    if (navigationViewModel.currentScreen is Screen.MatchCenter) {
                                        navigationViewModel.navigateBack()
                                    }
                                },
                                onNavigateToMatchEditor = {
                                    navigationViewModel.navigateTo(Screen.MatchEditor(screen.matchId))
                                },
                                onDeclareInnings = { runs, wickets, overs ->
                                    FixtureStore.fixtures.find { it.id == screen.matchId }?.let { f ->
                                        if (f.currentInnings == 1) {
                                            // Transition to 2nd Innings
                                            f.currentInnings = 2
                                            f.firstInningsRuns = runs
                                            f.firstInningsWickets = wickets
                                            
                                            // Reset score trackers for the 2nd Innings chase
                                            f.currentRuns = 0
                                            f.currentWickets = 0
                                            f.oversBowled = "0.0"
                                            f.strikerName = ""
                                            f.nonStrikerName = ""
                                            
                                            Toast.makeText(context, "1st Innings complete! Target set to ${runs + 1}. Starting 2nd Innings.", Toast.LENGTH_LONG).show()
                                            
                                            // Update the current screen state in place on the stack to reflect the new innings parameters
                                            navigationViewModel.updateCurrentScreen(screen, screen.copy(isScorer = true))
                                        } else {
                                            // Innings 2 declared - Match Completed
                                            f.status = "Completed"
                                            f.currentRuns = runs
                                            f.currentWickets = wickets
                                            f.oversBowled = overs
                                            Toast.makeText(context, "Match completed! Results saved.", Toast.LENGTH_LONG).show()
                                            // Pop back to Home
                                            navigationViewModel.clearAndNavigateTo(Screen.Home)
                                        }
                                    }
                                }
                            )
                        }
                        is Screen.PlayerProfile -> {
                            PlayerProfileScreen(
                                playerId = screen.playerId,
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen is Screen.PlayerProfile) {
                                        navigationViewModel.navigateBack()
                                    }
                                },
                                onStartScheduledMatch = { fixture ->
                                    if (fixture.status == "Live") {
                                        // Resume scoring directly
                                        navigationViewModel.navigateTo(
                                            Screen.MatchCenter(
                                                matchId = fixture.id,
                                                isScorer = true,
                                                homeSquadList = fixture.homeSquad,
                                                awaySquadList = fixture.awaySquad
                                            )
                                        )
                                    } else {
                                        fixture.status = "Live"
                                        navigationViewModel.navigateTo(
                                            Screen.Toss(
                                                matchId = fixture.id,
                                                homeTeam = fixture.homeTeam,
                                                awayTeam = fixture.awayTeam
                                            )
                                        )
                                    }
                                }
                            )
                        }
                        Screen.GlobalSearch -> {
                            GlobalSearchScreen(
                                onNavigateToPlayerProfile = { playerId ->
                                    navigationViewModel.navigateTo(Screen.PlayerProfile(playerId))
                                },
                                onNavigateToTeamDetail = { teamId ->
                                    navigationViewModel.navigateTo(Screen.TeamDetail(teamId))
                                },
                                onNavigateToTournamentHub = { tournamentId ->
                                    navigationViewModel.navigateTo(Screen.TournamentHub(tournamentId))
                                },
                                onNavigateToMatchCenter = { matchId ->
                                    navigationViewModel.navigateTo(Screen.MatchCenter(matchId = matchId, isScorer = false))
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen == Screen.GlobalSearch) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        is Screen.MatchEditor -> {
                            MatchEditorScreen(
                                matchId = screen.matchId,
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen is Screen.MatchEditor) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                        Screen.RecentMatches -> {
                            RecentMatchesScreen(
                                onNavigateToMatchCenter = { matchId ->
                                    navigationViewModel.navigateTo(Screen.MatchCenter(matchId = matchId, isScorer = false))
                                },
                                onNavigateToTournamentHub = { tournamentId ->
                                    navigationViewModel.navigateTo(Screen.TournamentHub(tournamentId))
                                },
                                onNavigateBack = {
                                    if (navigationViewModel.currentScreen == Screen.RecentMatches) {
                                        navigationViewModel.navigateBack()
                                    }
                                }
                            )
                        }
                    }
                }
            }
        }
    }
}