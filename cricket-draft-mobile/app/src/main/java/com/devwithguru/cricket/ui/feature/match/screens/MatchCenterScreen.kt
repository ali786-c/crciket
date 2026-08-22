package com.devwithguru.cricket.ui.feature.match.screens

import androidx.compose.ui.res.stringResource
import com.devwithguru.cricket.R
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.devwithguru.cricket.ui.feature.match.scorer.LiveScorerScreen
import com.devwithguru.cricket.ui.feature.match.scorer.LiveScorerViewModel
import com.devwithguru.cricket.ui.feature.match.viewmodels.MatchCenterViewModel
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.compose.runtime.collectAsState

// Shared Data Models for Scorecard and MVP listings
data class BattingCardRow(
    val name: String,
    val status: String,
    val r: Int,
    val b: Int,
    val fours: Int,
    val sixes: Int,
    val sr: String
)

data class BowlingCardRow(
    val name: String,
    val o: String,
    val m: Int,
    val r: Int,
    val w: Int,
    val econ: String
)

data class MVPStarRow(
    val rank: Int,
    val name: String,
    val stat: String,
    val pts: Int
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MatchCenterScreen(
    matchId: String,
    isScorer: Boolean = false,
    homeSquadList: List<String> = emptyList(),
    awaySquadList: List<String> = emptyList(),
    onNavigateBack: () -> Unit,
    onNavigateToMatchEditor: () -> Unit = {},
    onDeclareInnings: (runs: Int, wickets: Int, overs: String) -> Unit = { _, _, _ -> },
    matchCenterViewModel: MatchCenterViewModel = hiltViewModel(),
    scorerViewModel: LiveScorerViewModel = remember { LiveScorerViewModel() }
) {
    LaunchedEffect(matchId) { matchCenterViewModel.loadFixture(matchId) }
    val activeFixture by matchCenterViewModel.fixture.collectAsState()
    var currentInnings by remember { mutableStateOf(activeFixture?.currentInnings ?: 1) }
    var firstInningsTargetScore by remember { mutableStateOf(activeFixture?.firstInningsRuns?.plus(1)) }
    var selectedTab by remember { mutableStateOf(0) }

    val scoringTab = stringResource(R.string.tab_scoring)
    val scorecardTab = stringResource(R.string.tab_scorecard)
    val statsTab = stringResource(R.string.tab_stats)
    val superStarsTab = stringResource(R.string.tab_super_stars)
    val summaryTab = stringResource(R.string.tab_summary)

    val tabTitles = remember(isScorer, scoringTab, scorecardTab, statsTab, superStarsTab, summaryTab) {
        if (isScorer) {
            listOf(scoringTab, scorecardTab, statsTab, superStarsTab)
        } else {
            listOf(summaryTab, scorecardTab, statsTab, superStarsTab)
        }
    }

    val homeTeam = activeFixture?.homeTeam ?: "TBD"
    val awayTeam = activeFixture?.awayTeam ?: "TBD"

    Scaffold(
        topBar = {
            CenterAlignedTopAppBar(
                title = {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(
                            text = stringResource(R.string.match_center_title),
                            fontSize = 16.sp,
                            fontWeight = FontWeight.Bold,
                            color = MaterialTheme.colorScheme.onSurface
                        )
                        Text(
                            text = "$homeTeam vs $awayTeam",
                            fontSize = 11.sp,
                            color = MaterialTheme.colorScheme.primary,
                            fontWeight = FontWeight.Medium,
                            maxLines = 1,
                            overflow = TextOverflow.Ellipsis
                        )
                    }
                },
                navigationIcon = {
                    IconButton(onClick = onNavigateBack) {
                        Icon(
                            imageVector = Icons.Default.ArrowBack,
                            contentDescription = stringResource(R.string.back_desc),
                            tint = MaterialTheme.colorScheme.onBackground
                        )
                    }
                },
                colors = TopAppBarDefaults.centerAlignedTopAppBarColors(
                    containerColor = MaterialTheme.colorScheme.background
                )
            )
        },
        containerColor = MaterialTheme.colorScheme.background
    ) { innerPadding ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
                .background(MaterialTheme.colorScheme.background)
        ) {
            // Background radial gradient
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(
                        Brush.radialGradient(
                            colors = listOf(
                                MaterialTheme.colorScheme.primary.copy(alpha = 0.05f),
                                Color.Transparent
                            ),
                            radius = 1000f
                        )
                    )
            )

            Column(modifier = Modifier.fillMaxSize()) {
                ScrollableTabRow(
                    selectedTabIndex = selectedTab,
                    containerColor = MaterialTheme.colorScheme.background,
                    contentColor = MaterialTheme.colorScheme.primary,
                    edgePadding = 16.dp,
                    divider = { HorizontalDivider(color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.08f)) }
                ) {
                    tabTitles.forEachIndexed { index, title ->
                        Tab(
                            selected = selectedTab == index,
                            onClick = { selectedTab = index },
                            text = {
                                Text(
                                    text = title,
                                    fontSize = 13.sp,
                                    fontWeight = if (selectedTab == index) FontWeight.Bold else FontWeight.Normal
                                )
                            }
                        )
                    }
                }

                // Tab Contents
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1f)
                ) {
                    when (selectedTab) {
                        0 -> {
                            if (isScorer) {
                                val isFixtureInnings2 = currentInnings == 2

                                val battingTeam = if (isFixtureInnings2) activeFixture?.awayTeam ?: awayTeam else activeFixture?.homeTeam ?: homeTeam
                                val bowlingTeam = if (isFixtureInnings2) activeFixture?.homeTeam ?: homeTeam else activeFixture?.awayTeam ?: awayTeam

                                LiveScorerScreen(
                                    homeTeamName = battingTeam,
                                    awayTeamName = bowlingTeam,
                                    homeSquadList = if (isFixtureInnings2) awaySquadList else homeSquadList,
                                    awaySquadList = if (isFixtureInnings2) homeSquadList else awaySquadList,
                                    ballsPerOver = 6,
                                    initialRuns = activeFixture?.currentRuns ?: 0,
                                    initialWickets = activeFixture?.currentWickets ?: 0,
                                    initialOversBowled = activeFixture?.oversBowled ?: "0.0",
                                    initialStrikerName = activeFixture?.strikerName ?: "",
                                    initialNonStrikerName = activeFixture?.nonStrikerName ?: "",
                                    initialBowlerName = activeFixture?.bowlerName ?: "",
                                    initialBatsmenStats = if (isFixtureInnings2) activeFixture?.secondInningsBatsmen ?: emptyList() else activeFixture?.firstInningsBatsmen ?: emptyList(),
                                    initialBowlersStats = if (isFixtureInnings2) activeFixture?.secondInningsBowlers ?: emptyList() else activeFixture?.firstInningsBowlers ?: emptyList(),
                                    initialFOW = if (isFixtureInnings2) activeFixture?.secondInningsFOW ?: emptyList() else activeFixture?.firstInningsFOW ?: emptyList(),
                                    initialPartnerships = if (isFixtureInnings2) activeFixture?.secondInningsPartnerships ?: emptyList() else activeFixture?.firstInningsPartnerships ?: emptyList(),
                                    initialActivePartnershipRuns = activeFixture?.activePartnershipRuns ?: 0,
                                    initialActivePartnershipBalls = activeFixture?.activePartnershipBalls ?: 0,
                                    isInnings2 = isFixtureInnings2,
                                    firstInningsTarget = firstInningsTargetScore,
                                    matchTotalOvers = activeFixture?.overs ?: 6,
                                    matchTotalWickets = activeFixture?.wickets ?: 10,
                                    onDeclareInnings = { runs, wickets, overs ->
                                        activeFixture?.let { f ->
                                             if (f.currentInnings == 1) {
                                                 f.firstInningsBatsmen = scorerViewModel.state.batsmenStats.values.toList()
                                                 f.firstInningsBowlers = scorerViewModel.state.bowlersStats.values.toList()
                                                 f.firstInningsExtras = scorerViewModel.state.extras
                                                 f.firstInningsDotBalls = scorerViewModel.state.dotBalls
                                                 f.firstInningsFOW = scorerViewModel.state.fallOfWickets
                                                 f.firstInningsPartnerships = scorerViewModel.state.partnerships

                                                 currentInnings = 2
                                                 firstInningsTargetScore = runs + 1
                                             } else {
                                                 f.secondInningsBatsmen = scorerViewModel.state.batsmenStats.values.toList()
                                                 f.secondInningsBowlers = scorerViewModel.state.bowlersStats.values.toList()
                                                 f.secondInningsExtras = scorerViewModel.state.extras
                                                 f.secondInningsDotBalls = scorerViewModel.state.dotBalls
                                                 f.secondInningsFOW = scorerViewModel.state.fallOfWickets
                                                 f.secondInningsPartnerships = scorerViewModel.state.partnerships
                                             }
                                             // Persist scoring data to Room via MatchCenterViewModel
                                             matchCenterViewModel.updateFixture(f)
                                        }
                                        onDeclareInnings(runs, wickets, overs)
                                    },
                                    onNavigateToMatchEditor = onNavigateToMatchEditor,
                                    onNavigateBack = onNavigateBack,
                                    viewModel = scorerViewModel,
                                    onScoreChanged = { runs, wickets, overs, striker, nonStriker ->
                                        activeFixture?.let { f ->
                                            f.currentRuns = runs
                                            f.currentWickets = wickets
                                            f.oversBowled = overs
                                            f.strikerName = striker
                                            f.nonStrikerName = nonStriker
                                            f.bowlerName = scorerViewModel.state.bowler.name
                                            f.activePartnershipRuns = scorerViewModel.state.activePartnershipRuns
                                            f.activePartnershipBalls = scorerViewModel.state.activePartnershipBalls

                                            if (f.currentInnings == 1) {
                                                f.firstInningsBatsmen = scorerViewModel.state.batsmenStats.values.toList()
                                                f.firstInningsBowlers = scorerViewModel.state.bowlersStats.values.toList()
                                                f.firstInningsFOW = scorerViewModel.state.fallOfWickets
                                                f.firstInningsPartnerships = scorerViewModel.state.partnerships
                                            } else {
                                                f.secondInningsBatsmen = scorerViewModel.state.batsmenStats.values.toList()
                                                f.secondInningsBowlers = scorerViewModel.state.bowlersStats.values.toList()
                                                f.secondInningsFOW = scorerViewModel.state.fallOfWickets
                                                f.secondInningsPartnerships = scorerViewModel.state.partnerships
                                            }
                                        }
                                    }
                                )
                            } else {
                                MatchSummaryTab(homeTeam, awayTeam, activeFixture)
                            }
                        }
                        1 -> MatchScorecardTab(matchId = matchId, homeTeam = homeTeam, awayTeam = awayTeam, viewModel = scorerViewModel, activeFixture = activeFixture)
                        2 -> MatchStatsTab(matchId = matchId, homeTeam = homeTeam, awayTeam = awayTeam, viewModel = scorerViewModel, activeFixture = activeFixture)
                        3 -> MatchSuperStarsTab(matchId = matchId, viewModel = scorerViewModel, activeFixture = activeFixture)
                    }
                }
            }
        }
    }
}

// Shared UI element for stats comparisons
@Composable
fun MatchStatCompareBar(
    label: String,
    homeVal: String,
    awayVal: String,
    homeRatio: Float,
    homeTeam: String,
    awayTeam: String
) {
    Column(
        modifier = Modifier.fillMaxWidth(),
        verticalArrangement = Arrangement.spacedBy(6.dp)
    ) {
        Text(
            text = label,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
            fontSize = 11.sp,
            fontWeight = FontWeight.Bold
        )

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
            Text(text = homeVal, color = MaterialTheme.colorScheme.onSurface, fontWeight = FontWeight.Bold, fontSize = 13.sp)
            Text(text = awayVal, color = MaterialTheme.colorScheme.onSurface, fontWeight = FontWeight.Bold, fontSize = 13.sp)
        }

        // Custom comparison progress bar using dynamic weights
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .height(8.dp)
                .clip(RoundedCornerShape(4.dp))
                .background(MaterialTheme.colorScheme.onSurface.copy(alpha = 0.08f))
        ) {
            Box(
                modifier = Modifier
                    .fillMaxHeight()
                    .weight(homeRatio.coerceAtLeast(0.01f))
                    .background(MaterialTheme.colorScheme.primary)
            )
            Box(
                modifier = Modifier
                    .fillMaxHeight()
                    .weight((1f - homeRatio).coerceAtLeast(0.01f))
                    .background(MaterialTheme.colorScheme.onSurface.copy(alpha = 0.2f))
            )
        }
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
            Text(text = homeTeam, color = MaterialTheme.colorScheme.onSurfaceVariant, fontSize = 9.sp)
            Text(text = awayTeam, color = MaterialTheme.colorScheme.onSurfaceVariant, fontSize = 9.sp)
        }
    }
}
