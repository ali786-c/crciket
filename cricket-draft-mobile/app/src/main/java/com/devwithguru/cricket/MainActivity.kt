package com.devwithguru.cricket

import android.os.Bundle
import android.widget.Toast
import com.devwithguru.cricket.R
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.compose.BackHandler
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.runtime.*
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.res.stringResource
import com.devwithguru.cricket.ui.feature.auth.LoginScreen
import com.devwithguru.cricket.ui.feature.auth.PlayerOnboardingScreen
import com.devwithguru.cricket.ui.feature.home.UnifiedHomeScreen
import com.devwithguru.cricket.ui.feature.match.screens.CreateMatchScreen
import com.devwithguru.cricket.ui.feature.tournament.CreateTournamentScreen
import com.devwithguru.cricket.ui.feature.tournament.MyTournamentsScreen
import com.devwithguru.cricket.ui.feature.tournament.TournamentHubScreen
import com.devwithguru.cricket.ui.feature.team.TeamDetailScreen
import com.devwithguru.cricket.ui.feature.match.toss.TossLineupScreen
import com.devwithguru.cricket.ui.feature.match.toss.TossScreen
import com.devwithguru.cricket.ui.feature.match.scorer.LiveScorerScreen
import com.devwithguru.cricket.ui.feature.match.screens.MatchCenterScreen
import com.devwithguru.cricket.ui.feature.player.PlayerProfileScreen
import com.devwithguru.cricket.ui.feature.home.GlobalSearchScreen
import com.devwithguru.cricket.ui.feature.match.screens.MatchEditorScreen
import com.devwithguru.cricket.ui.feature.match.RecentMatchesScreen
import com.devwithguru.cricket.domain.model.ScheduledFixture
import com.devwithguru.cricket.ui.viewmodels.MainViewModel
import androidx.hilt.navigation.compose.hiltViewModel
import dagger.hilt.android.AndroidEntryPoint
import com.devwithguru.cricket.ui.feature.match.scorer.LiveScorerViewModel
import com.devwithguru.cricket.ui.theme.CricketTheme
import com.devwithguru.cricket.ui.navigation.Screen
import com.devwithguru.cricket.ui.viewmodels.NavigationViewModel

@AndroidEntryPoint
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
                    val mainViewModel: MainViewModel = hiltViewModel()
                    val currentFixture by mainViewModel.currentFixture.collectAsState()
                    val context = LocalContext.current

                    val msgNavigateMyTeams = stringResource(R.string.msg_navigate_my_teams)
                    val msgLoggedOut = stringResource(R.string.msg_logged_out)
                    val msgMatchSetupComplete = stringResource(R.string.msg_match_setup_complete)
                    val msgMatchCompletedResultsSaved = stringResource(R.string.msg_match_completed_results_saved)

                    // For formatted strings, we'll need to use context.getString in the lambda,
                    // but maybe the lint rule allows it if we don't have another choice,
                    // OR we can get the format string using stringResource and then format it.
                    val msgLoggedInFormat = stringResource(R.string.msg_logged_in)
                    val msgRegisteredFormat = stringResource(R.string.msg_registered)
                    val msgCreatedTournamentFormat = stringResource(R.string.msg_created_tournament)
                    val msgInningsCompleteFormat = stringResource(R.string.msg_innings_complete)

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
                                    Toast.makeText(context, String.format(msgLoggedInFormat, email), Toast.LENGTH_SHORT).show()
                                },
                                onNavigateToRegister = {
                                    navigationViewModel.navigateTo(Screen.Onboarding)
                                }
                            )
                        }
                        Screen.Onboarding -> {
                            PlayerOnboardingScreen(
                                onSubmitRegistration = { name, role, batting, bowling, city, bio ->
                                    Toast.makeText(context, String.format(msgRegisteredFormat, name, role), Toast.LENGTH_LONG).show()
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
                                    Toast.makeText(context, msgNavigateMyTeams, Toast.LENGTH_SHORT).show()
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
                                    Toast.makeText(context, msgLoggedOut, Toast.LENGTH_SHORT).show()
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
                                    Toast.makeText(context, String.format(msgCreatedTournamentFormat, name, city, season, ballType), Toast.LENGTH_LONG).show()
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
                            LaunchedEffect(screen.matchId) { mainViewModel.loadFixture(screen.matchId) }
                            TossLineupScreen(
                                homeTeamName = screen.homeTeam,
                                awayTeamName = screen.awayTeam,
                                tossWinner = screen.tossWinner,
                                tossDecision = screen.tossDecision,
                                onStartMatchSuccess = { winner, decision, homeLineup, awayLineup ->
                                    // Update fixture status via ViewModel
                                    val fixture = mainViewModel.getFixture(screen.matchId) ?: currentFixture?.takeIf { it.id == screen.matchId }
                                    fixture?.let { f ->
                                        f.status = "Live"
                                        f.homeSquad = homeLineup
                                        f.awaySquad = awayLineup
                                        mainViewModel.updateFixture(f)
                                    }
                                    Toast.makeText(context, msgMatchSetupComplete, Toast.LENGTH_SHORT).show()
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
                            LaunchedEffect(screen.matchId) { mainViewModel.loadFixture(screen.matchId) }
                            MatchCenterScreen(
                                matchId = screen.matchId,
                                isScorer = screen.isScorer,
                                homeSquadList = screen.homeSquadList,
                                awaySquadList = screen.awaySquadList,
                                scorerViewModel = scorerViewModel,
                                onNavigateBack = {
                                    val fixture = mainViewModel.getFixture(screen.matchId) ?: currentFixture?.takeIf { it.id == screen.matchId }
                                    fixture?.let { f ->
                                        if (f.status != "Completed") {
                                            f.status = "Live"
                                            mainViewModel.updateFixture(f)
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
                                    val fixture = mainViewModel.getFixture(screen.matchId) ?: currentFixture?.takeIf { it.id == screen.matchId }
                                    fixture?.let { f ->
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

                                            Toast.makeText(context, String.format(msgInningsCompleteFormat, runs + 1), Toast.LENGTH_LONG).show()

                                            // Update the current screen state in place on the stack to reflect the new innings parameters
                                            navigationViewModel.updateCurrentScreen(screen, screen.copy(isScorer = true))
                                        } else {
                                            // Innings 2 declared - Match Completed
                                            f.status = "Completed"
                                            f.currentRuns = runs
                                            f.currentWickets = wickets
                                            f.oversBowled = overs
                                            Toast.makeText(context, msgMatchCompletedResultsSaved, Toast.LENGTH_LONG).show()
                                            // Pop back to Home
                                            navigationViewModel.clearAndNavigateTo(Screen.Home)
                                        }
                                        // Persist fixture changes to Room
                                        mainViewModel.updateFixture(f)
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
