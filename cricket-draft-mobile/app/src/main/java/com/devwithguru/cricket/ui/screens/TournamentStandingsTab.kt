package com.devwithguru.cricket.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Shield
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

data class StandingRow(
    val pos: Int,
    val team: String,
    val p: Int,
    val w: Int,
    val l: Int,
    val pts: Int,
    val nrr: String
)

@Composable
fun TournamentStandingsTab() {
    val standings = listOf(
        StandingRow(1, "Islamabad Blasters", 3, 3, 0, 6, "+1.24"),
        StandingRow(2, "Ali Panthers", 3, 2, 1, 4, "+0.45"),
        StandingRow(3, "Rawalpindi Kings", 3, 1, 2, 2, "-0.15"),
        StandingRow(4, "Karachi Tigers", 3, 0, 3, 0, "-1.54")
    )

    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Text(
            text = "Tournament Standings",
            color = MaterialTheme.colorScheme.primary,
            fontSize = 14.sp,
            fontWeight = FontWeight.Bold
        )

        Card(
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, MaterialTheme.colorScheme.onSurface.copy(alpha = 0.08f), RoundedCornerShape(12.dp)),
            colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant),
            shape = RoundedCornerShape(12.dp)
        ) {
            Column(modifier = Modifier.padding(8.dp)) {
                // Table Header Row
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(MaterialTheme.colorScheme.onSurface.copy(alpha = 0.02f))
                        .padding(vertical = 8.dp, horizontal = 4.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(text = "Pos", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold, fontSize = 12.sp, modifier = Modifier.weight(1f))
                    Text(text = "Team", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold, fontSize = 12.sp, modifier = Modifier.weight(4f))
                    Text(text = "P", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold, fontSize = 12.sp, modifier = Modifier.weight(1f), textAlign = TextAlign.Center)
                    Text(text = "W", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold, fontSize = 12.sp, modifier = Modifier.weight(1f), textAlign = TextAlign.Center)
                    Text(text = "L", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold, fontSize = 12.sp, modifier = Modifier.weight(1f), textAlign = TextAlign.Center)
                    Text(text = "Pts", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold, fontSize = 12.sp, modifier = Modifier.weight(1.5f), textAlign = TextAlign.Center)
                    Text(text = "NRR", color = MaterialTheme.colorScheme.primary, fontWeight = FontWeight.Bold, fontSize = 12.sp, modifier = Modifier.weight(2f), textAlign = TextAlign.End)
                }

                HorizontalDivider(color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.15f))

                // Table Rows
                standings.forEach { row ->
                    val isQualifying = row.pos <= 2
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .background(if (isQualifying) MaterialTheme.colorScheme.primary.copy(alpha = 0.03f) else Color.Transparent)
                            .padding(vertical = 12.dp, horizontal = 4.dp),
                        horizontalArrangement = Arrangement.SpaceBetween,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text(text = "${row.pos}", color = if (isQualifying) MaterialTheme.colorScheme.primary else MaterialTheme.colorScheme.onSurface, fontWeight = if (isQualifying) FontWeight.Bold else FontWeight.Normal, fontSize = 12.sp, modifier = Modifier.weight(1f))
                        Row(
                            verticalAlignment = Alignment.CenterVertically,
                            horizontalArrangement = Arrangement.spacedBy(4.dp),
                            modifier = Modifier.weight(4f)
                        ) {
                            Icon(
                                imageVector = Icons.Default.Shield,
                                contentDescription = null,
                                tint = if (isQualifying) MaterialTheme.colorScheme.primary else MaterialTheme.colorScheme.onSurface.copy(alpha = 0.4f),
                                modifier = Modifier.size(12.dp)
                            )
                            Text(
                                text = row.team,
                                color = MaterialTheme.colorScheme.onSurface,
                                fontWeight = if (isQualifying) FontWeight.Bold else FontWeight.Normal,
                                fontSize = 12.sp
                            )
                        }
                        Text(text = "${row.p}", color = MaterialTheme.colorScheme.onSurface, fontSize = 12.sp, modifier = Modifier.weight(1f), textAlign = TextAlign.Center)
                        Text(text = "${row.w}", color = MaterialTheme.colorScheme.onSurface, fontSize = 12.sp, modifier = Modifier.weight(1f), textAlign = TextAlign.Center)
                        Text(text = "${row.l}", color = MaterialTheme.colorScheme.onSurface, fontSize = 12.sp, modifier = Modifier.weight(1f), textAlign = TextAlign.Center)
                        Text(text = "${row.pts}", color = if (isQualifying) MaterialTheme.colorScheme.primary else MaterialTheme.colorScheme.onSurface, fontWeight = FontWeight.Bold, fontSize = 12.sp, modifier = Modifier.weight(1.5f), textAlign = TextAlign.Center)
                        Text(text = row.nrr, color = MaterialTheme.colorScheme.onSurface, fontSize = 12.sp, modifier = Modifier.weight(2f), textAlign = TextAlign.End)
                    }
                    HorizontalDivider(color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.08f))
                }
            }
        }
    }
}
