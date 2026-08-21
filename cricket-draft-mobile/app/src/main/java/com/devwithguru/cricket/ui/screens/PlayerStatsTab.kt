package com.devwithguru.cricket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

@Composable
fun PlayerStatsTab() {
    var activeSubTab by remember { mutableStateOf("BAT") }
    val subTabs = listOf("BAT", "BOWL", "FIELD", "MATCH-WISE")

    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(horizontal = 16.dp, vertical = 8.dp),
        verticalArrangement = Arrangement.spacedBy(4.dp)
    ) {
        // Segmented Sub-Tabs Row (BAT, BOWL, FIELD, MATCH-WISE) - Compact & Non-wrapping
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .clip(RoundedCornerShape(8.dp))
                .background(MaterialTheme.colorScheme.onSurface.copy(alpha = 0.05f))
                .border(1.dp, MaterialTheme.colorScheme.onSurface.copy(alpha = 0.08f), RoundedCornerShape(8.dp)),
            horizontalArrangement = Arrangement.SpaceEvenly,
            verticalAlignment = Alignment.CenterVertically
        ) {
            subTabs.forEach { tab ->
                val isActive = activeSubTab == tab
                Box(
                    modifier = Modifier
                        .weight(1f)
                        .clickable { activeSubTab = tab }
                        .background(if (isActive) MaterialTheme.colorScheme.secondary else Color.Transparent)
                        .padding(vertical = 8.dp, horizontal = 2.dp),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = tab,
                        fontSize = 9.sp,
                        fontWeight = FontWeight.Bold,
                        fontFamily = FontFamily.SansSerif,
                        letterSpacing = (-0.2).sp,
                        maxLines = 1,
                        color = if (isActive) MaterialTheme.colorScheme.onSecondary else MaterialTheme.colorScheme.onSurfaceVariant
                    )
                }
            }
        }

        // Table Content
        when (activeSubTab) {
            "BAT" -> {
                TableHeaderRow()
                TableDataRow("Matches", "42", "58", "12", "4")
                TableDataRow("Innings", "38", "52", "11", "3")
                TableDataRow("Runs", "782", "1,245", "310", "112")
                TableDataRow("Balls", "518", "914", "350", "145")
                TableDataRow("Highest", "56*", "84*", "76", "42")
                TableDataRow("Average", "24.4", "28.5", "31.0", "37.3")
                TableDataRow("SR", "150.9", "136.2", "88.4", "77.2")
                TableDataRow("Not Out", "6", "8", "1", "1")
                TableDataRow("Ducks", "2", "3", "0", "0")
                TableDataRow("100s", "0", "0", "0", "0")
                TableDataRow("50s", "3", "6", "2", "0")
                TableDataRow("30s", "8", "12", "4", "1")
                TableDataRow("6s", "24", "38", "5", "1")
                TableDataRow("4s", "36", "58", "10", "3")
            }
            "BOWL" -> {
                TableHeaderRow()
                TableDataRow("Matches", "42", "58", "12", "4")
                TableDataRow("Innings", "36", "48", "10", "2")
                TableDataRow("Overs", "112.4", "148.2", "42.0", "14.0")
                TableDataRow("Wickets", "32", "48", "10", "3")
                TableDataRow("Runs Conc", "842", "1,162", "235", "78")
                TableDataRow("Economy", "7.47", "7.84", "5.60", "5.57")
                TableDataRow("Average", "26.3", "24.2", "23.5", "26.0")
                TableDataRow("SR", "21.1", "18.5", "25.2", "28.0")
                TableDataRow("5w", "0", "1", "0", "0")
                TableDataRow("Best Fig", "3/18", "4/18", "3/42", "2/24")
                TableDataRow("Wides", "14", "22", "6", "2")
                TableDataRow("No Balls", "3", "5", "1", "0")
            }
            "FIELD" -> {
                TableHeaderRow()
                TableDataRow("Matches", "42", "58", "12", "4")
                TableDataRow("Innings", "42", "58", "12", "4")
                TableDataRow("Catches", "14", "22", "4", "1")
                TableDataRow("Run Outs", "3", "5", "1", "0")
                TableDataRow("Stumpings", "0", "0", "0", "0")
                TableDataRow("Direct Hits", "2", "4", "1", "0")
            }
            "MATCH-WISE" -> {
                Column(
                    modifier = Modifier.padding(top = 8.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(
                        text = "Match-Wise Performances",
                        fontSize = 13.sp,
                        fontWeight = FontWeight.SemiBold,
                        fontFamily = FontFamily.SansSerif,
                        color = MaterialTheme.colorScheme.primary,
                        modifier = Modifier.padding(vertical = 2.dp)
                    )

                    MatchPerformanceItem("vs Islamabad Blasters", "32 (24)", "T20", "Aug 15, 2026")
                    MatchPerformanceItem("vs Rawalpindi Kings", "56* (32)", "T20", "Aug 12, 2026")
                    MatchPerformanceItem("vs Lahore Qalandars", "12 (8)", "T10", "Aug 10, 2026")
                    MatchPerformanceItem("vs Karachi Tigers", "44 (30)", "T20", "Aug 05, 2026")
                    MatchPerformanceItem("vs Peshawar Stars", "18 (14)", "T10", "Jul 28, 2026")
                }
            }
        }
    }
}

@Composable
fun TableHeaderRow() {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(top = 6.dp, bottom = 2.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Spacer(modifier = Modifier.weight(1.5f))
        Text(
            text = "T10",
            color = MaterialTheme.colorScheme.primary,
            fontSize = 12.sp,
            fontWeight = FontWeight.Bold,
            fontFamily = FontFamily.SansSerif,
            textAlign = TextAlign.Center,
            modifier = Modifier.weight(1f)
        )
        Text(
            text = "T20",
            color = MaterialTheme.colorScheme.primary,
            fontSize = 12.sp,
            fontWeight = FontWeight.Bold,
            fontFamily = FontFamily.SansSerif,
            textAlign = TextAlign.Center,
            modifier = Modifier.weight(1f)
        )
        Text(
            text = "Club",
            color = MaterialTheme.colorScheme.primary,
            fontSize = 12.sp,
            fontWeight = FontWeight.Bold,
            fontFamily = FontFamily.SansSerif,
            textAlign = TextAlign.Center,
            modifier = Modifier.weight(1f)
        )
        Text(
            text = "OD",
            color = MaterialTheme.colorScheme.primary,
            fontSize = 12.sp,
            fontWeight = FontWeight.Bold,
            fontFamily = FontFamily.SansSerif,
            textAlign = TextAlign.Center,
            modifier = Modifier.weight(1f)
        )
    }
}

@Composable
fun TableDataRow(
    label: String,
    valT10: String,
    valT20: String,
    valClub: String,
    valOD: String
) {
    Column {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(vertical = 5.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                text = label,
                color = MaterialTheme.colorScheme.onSurface,
                fontSize = 12.sp,
                fontWeight = FontWeight.Medium,
                fontFamily = FontFamily.SansSerif,
                modifier = Modifier.weight(1.5f)
            )
            Text(
                text = valT10,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
                fontSize = 12.sp,
                fontWeight = FontWeight.Normal,
                fontFamily = FontFamily.SansSerif,
                textAlign = TextAlign.Center,
                modifier = Modifier.weight(1f)
            )
            Text(
                text = valT20,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
                fontSize = 12.sp,
                fontWeight = FontWeight.Normal,
                fontFamily = FontFamily.SansSerif,
                textAlign = TextAlign.Center,
                modifier = Modifier.weight(1f)
            )
            Text(
                text = valClub,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
                fontSize = 12.sp,
                fontWeight = FontWeight.Normal,
                fontFamily = FontFamily.SansSerif,
                textAlign = TextAlign.Center,
                modifier = Modifier.weight(1f)
            )
            Text(
                text = valOD,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
                fontSize = 12.sp,
                fontWeight = FontWeight.Normal,
                fontFamily = FontFamily.SansSerif,
                textAlign = TextAlign.Center,
                modifier = Modifier.weight(1f)
            )
        }
        HorizontalDivider(color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.08f))
    }
}

@Composable
fun MatchPerformanceItem(
    opponent: String,
    score: String,
    format: String,
    date: String
) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant),
        border = androidx.compose.foundation.BorderStroke(1.dp, MaterialTheme.colorScheme.onSurface.copy(alpha = 0.08f))
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(12.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                Text(
                    text = opponent,
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    fontFamily = FontFamily.SansSerif,
                    color = MaterialTheme.colorScheme.onSurface
                )
                Row(
                    horizontalArrangement = Arrangement.spacedBy(8.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Box(
                        modifier = Modifier
                            .background(MaterialTheme.colorScheme.primary.copy(alpha = 0.1f), RoundedCornerShape(4.dp))
                            .padding(horizontal = 6.dp, vertical = 2.dp)
                    ) {
                        Text(format, color = MaterialTheme.colorScheme.primary, fontSize = 9.sp, fontWeight = FontWeight.Bold, fontFamily = FontFamily.SansSerif)
                    }
                    Text(date, color = MaterialTheme.colorScheme.onSurfaceVariant, fontSize = 11.sp, fontFamily = FontFamily.SansSerif)
                }
            }

            Text(
                text = score,
                fontSize = 14.sp,
                fontWeight = FontWeight.ExtraBold,
                fontFamily = FontFamily.SansSerif,
                color = MaterialTheme.colorScheme.primary
            )
        }
    }
}
