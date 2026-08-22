package com.devwithguru.cricket.ui.feature.home

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Clear
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.History
import androidx.compose.material.icons.filled.KeyboardArrowRight
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.Shield
import androidx.compose.material.icons.filled.SportsCricket
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel

data class SearchItem(
    val id: String,
    val type: String, // "player", "team", "match", "tournament"
    val title: String,
    val subtitle: String
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun GlobalSearchScreen(
    onNavigateToPlayerProfile: (playerId: String) -> Unit,
    onNavigateToTeamDetail: (teamId: String) -> Unit,
    onNavigateToTournamentHub: (tournamentId: String) -> Unit,
    onNavigateToMatchCenter: (matchId: String) -> Unit,
    onNavigateBack: () -> Unit
,
    viewModel: com.devwithguru.cricket.ui.viewmodels.MainViewModel = hiltViewModel()
) {
    var searchQuery by remember { mutableStateOf("") }

    // Unified Mock Database
    val mockDatabase = remember {
        listOf(
            SearchItem("h1", "player", "Ahmed Ali", "Ali Panthers • All-rounder"),
            SearchItem("h2", "player", "Bilal Butt", "Ali Panthers • Batter"),
            SearchItem("h3", "player", "Salman Ahmed", "Ali Panthers • Wicketkeeper"),
            SearchItem("h4", "player", "Usman Shinwari", "Ali Panthers • Bowler"),
            SearchItem("a1", "player", "Yasir Khan", "Islamabad Blasters • Bowler"),
            SearchItem("a2", "player", "Babar Azam", "Islamabad Blasters • Batter"),
            SearchItem("a3", "player", "Mohammad Rizwan", "Islamabad Blasters • Wicketkeeper"),
            SearchItem("a4", "player", "Shaheen Afridi", "Islamabad Blasters • Bowler"),

            SearchItem("1", "team", "Ali Panthers", "Active in Premier Cricket Cup 2026"),
            SearchItem("2", "team", "Rawalpindi Kings", "Active in Premier Cricket Cup 2026"),
            SearchItem("3", "team", "Islamabad Blasters", "Active in Premier Cricket Cup 2026"),
            SearchItem("4", "team", "Karachi Tigers", "Active in Premier Cricket Cup 2026"),
            SearchItem("5", "team", "Lahore Qalandars", "Active in local cups"),
            SearchItem("6", "team", "Peshawar Stars", "Active in local cups"),

            SearchItem("1", "tournament", "Premier Cricket Cup 2026", "Live & Active group stages"),
            SearchItem("2", "tournament", "Summer Smash 2025", "Concluded tournament"),
            SearchItem("3", "tournament", "Super League 2024", "Concluded tournament"),

            SearchItem("1", "match", "Ali Panthers vs Rawalpindi Kings", "Completed - Ali Panthers won by 4 runs"),
            SearchItem("2", "match", "Blasters vs Karachi Tigers", "Completed - Blasters won by 7 wickets"),
            SearchItem("3", "match", "Lahore Qalandars vs Peshawar Stars", "Upcoming - Aug 20, 2026")
        )
    }

    // Filter results
    val filteredResults = remember(searchQuery) {
        if (searchQuery.trim().isEmpty()) {
            emptyList()
        } else {
            mockDatabase.filter {
                it.title.contains(searchQuery, ignoreCase = true) ||
                        it.subtitle.contains(searchQuery, ignoreCase = true)
            }
        }
    }

    Scaffold(
        topBar = {
            Surface(
                modifier = Modifier.fillMaxWidth(),
                color = MaterialTheme.colorScheme.background,
                tonalElevation = 4.dp
            ) {
                Row(
                    modifier = Modifier
                        .statusBarsPadding()
                        .padding(horizontal = 8.dp, vertical = 12.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    IconButton(onClick = onNavigateBack) {
                        Icon(
                            imageVector = Icons.Default.ArrowBack,
                            contentDescription = "Back",
                            tint = MaterialTheme.colorScheme.onBackground
                        )
                    }

                    OutlinedTextField(
                        value = searchQuery,
                        onValueChange = { searchQuery = it },
                        placeholder = { Text("Search players, teams, tournaments...", fontSize = 13.sp, color = MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.6f)) },
                        leadingIcon = { Icon(Icons.Default.Search, contentDescription = null, tint = MaterialTheme.colorScheme.primary, modifier = Modifier.size(20.dp)) },
                        trailingIcon = {
                            if (searchQuery.isNotEmpty()) {
                                IconButton(onClick = { searchQuery = "" }) {
                                    Icon(Icons.Default.Clear, contentDescription = "Clear", tint = Color.Gray, modifier = Modifier.size(20.dp))
                                }
                            }
                        },
                        modifier = Modifier
                            .weight(1f)
                            .height(56.dp),
                        shape = RoundedCornerShape(12.dp),
                        singleLine = true,
                        colors = OutlinedTextFieldDefaults.colors(
                            focusedBorderColor = MaterialTheme.colorScheme.primary,
                            unfocusedBorderColor = MaterialTheme.colorScheme.outline,
                            focusedContainerColor = MaterialTheme.colorScheme.surfaceVariant,
                            unfocusedContainerColor = MaterialTheme.colorScheme.surfaceVariant,
                            focusedTextColor = MaterialTheme.colorScheme.onSurface,
                            unfocusedTextColor = MaterialTheme.colorScheme.onSurface
                        )
                    )
                }
            }
        },
        containerColor = MaterialTheme.colorScheme.background
    ) { innerPadding ->
        Box(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
                .background(MaterialTheme.colorScheme.background)
        ) {
            // Background ambient glow
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

            LazyColumn(
                modifier = Modifier.fillMaxSize(),
                contentPadding = PaddingValues(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp)
            ) {
                if (searchQuery.trim().isEmpty()) {
                    // Recent Searches
                    item {
                        Text(
                            text = "Recent Searches",
                            color = MaterialTheme.colorScheme.primary,
                            fontSize = 13.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }

                    val recents = listOf(
                        SearchItem("h1", "player", "Ahmed Ali", "Ali Panthers"),
                        SearchItem("1", "team", "Ali Panthers", "Group Stage"),
                        SearchItem("1", "tournament", "Premier Cricket Cup 2026", "Live")
                    )

                    items(recents) { item ->
                        RecentSearchItemRow(item) {
                            searchQuery = item.title
                        }
                    }
                } else if (filteredResults.isEmpty()) {
                    // Empty Results State
                    item {
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(vertical = 40.dp),
                            contentAlignment = Alignment.Center
                        ) {
                            Text(
                                text = "No results found for \"$searchQuery\"",
                                color = MaterialTheme.colorScheme.onSurfaceVariant,
                                fontSize = 14.sp,
                                textAlign = TextAlign.Center
                            )
                        }
                    }
                } else {
                    // Grouped Search Results
                    val grouped = filteredResults.groupBy { it.type }

                    listOf("player", "team", "tournament", "match").forEach { type ->
                        val groupList = grouped[type]
                        if (!groupList.isNullOrEmpty()) {
                            item {
                                Text(
                                    text = when (type) {
                                        "player" -> "Players"
                                        "team" -> "Teams"
                                        "tournament" -> "Tournaments"
                                        else -> "Matches"
                                    },
                                    color = MaterialTheme.colorScheme.primary,
                                    fontSize = 12.sp,
                                    fontWeight = FontWeight.Bold,
                                    modifier = Modifier.padding(top = 8.dp)
                                )
                            }

                            items(groupList) { item ->
                                SearchResultItemCard(
                                    item = item,
                                    onClick = {
                                        when (item.type) {
                                            "player" -> onNavigateToPlayerProfile(item.id)
                                            "team" -> onNavigateToTeamDetail(item.id)
                                            "tournament" -> onNavigateToTournamentHub(item.id)
                                            "match" -> onNavigateToMatchCenter(item.id)
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
}

@Composable
fun RecentSearchItemRow(item: SearchItem, onClick: () -> Unit) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() }
            .padding(vertical = 12.dp, horizontal = 4.dp),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.SpaceBetween
    ) {
        Row(
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Icon(
                imageVector = Icons.Default.History,
                contentDescription = null,
                tint = MaterialTheme.colorScheme.onSurfaceVariant,
                modifier = Modifier.size(18.dp)
            )
            Text(
                text = item.title,
                color = MaterialTheme.colorScheme.onSurface,
                fontSize = 14.sp
            )
        }
        Icon(
            imageVector = Icons.Default.KeyboardArrowRight,
            contentDescription = null,
            tint = Color.Gray,
            modifier = Modifier.size(16.dp)
        )
    }
    HorizontalDivider(color = MaterialTheme.colorScheme.outline.copy(alpha = 0.5f))
}

@Composable
fun SearchResultItemCard(item: SearchItem, onClick: () -> Unit) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .border(1.dp, MaterialTheme.colorScheme.outline.copy(alpha = 0.5f), RoundedCornerShape(12.dp)),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant),
        shape = RoundedCornerShape(12.dp)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .clickable { onClick() }
                .padding(14.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.spacedBy(12.dp),
                modifier = Modifier.weight(1f)
            ) {
                // Dynamic Icon Category representation
                Box(
                    modifier = Modifier
                        .size(36.dp)
                        .clip(RoundedCornerShape(18.dp))
                        .background(MaterialTheme.colorScheme.outline.copy(alpha = 0.5f)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = when (item.type) {
                            "player" -> Icons.Default.Person
                            "team" -> Icons.Default.Shield
                            "tournament" -> Icons.Default.EmojiEvents
                            else -> Icons.Default.SportsCricket
                        },
                        contentDescription = null,
                        tint = MaterialTheme.colorScheme.primary,
                        modifier = Modifier.size(18.dp)
                    )
                }
                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text = item.title,
                        color = MaterialTheme.colorScheme.onSurface,
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Bold,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis
                    )
                    Text(
                        text = item.subtitle,
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        fontSize = 11.sp,
                        maxLines = 1,
                        overflow = TextOverflow.Ellipsis
                    )
                }
            }
            Icon(
                imageVector = Icons.Default.KeyboardArrowRight,
                contentDescription = null,
                tint = MaterialTheme.colorScheme.primary,
                modifier = Modifier.size(16.dp)
            )
        }
    }
}
