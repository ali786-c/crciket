package com.devwithguru.cricket.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.layout.RowScope
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
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

private val CardShape = RoundedCornerShape(16.dp)

@Composable
fun PremiumMatchCard(
    tournamentName: String?,
    stageInfo: String,
    teamA: String,
    teamB: String,
    scoreA: String,
    scoreB: String,
    isLive: Boolean = false,
    modifier: Modifier = Modifier,
    onClick: () -> Unit = {},
    onViewTournamentClick: () -> Unit = {}
) {
    // ── Green Gradient Colors ──
    val gradientTop = Color(0xFF2E7D32)
    val gradientBottom = Color(0xFF1B5E20)
    val accentLight = Color(0xFFA5D6A7)
    val accentMid = Color(0xFF81C784)

    // The key fix: Box gets same clip shape as Card so bottom corners match top
    Box(
        modifier = modifier
            .clip(CardShape)
            .clickable { onClick() }
            .background(
                Brush.verticalGradient(
                    colors = if (isLive) {
                        listOf(gradientTop, gradientBottom)
                    } else {
                        listOf(gradientTop.copy(alpha = 0.9f), gradientBottom)
                    }
                )
            )
            .border(1.dp, Color.White.copy(alpha = 0.1f), CardShape)
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 14.dp, vertical = 10.dp),
            verticalArrangement = Arrangement.spacedBy(4.dp)
        ) {
            // ── Header: Title + LIVE Badge ──
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text(
                    text = tournamentName ?: "",
                    color = Color.White.copy(alpha = 0.8f),
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Medium,
                    maxLines = 1,
                    overflow = TextOverflow.Ellipsis,
                    modifier = Modifier.weight(1f)
                )
                if (isLive) {
                    Spacer(modifier = Modifier.width(6.dp))
                    Surface(
                        shape = RoundedCornerShape(4.dp),
                        color = Color(0xFFE53935)
                    ) {
                        Text(
                            text = "LIVE",
                            color = Color.White,
                            fontSize = 8.sp,
                            fontWeight = FontWeight.ExtraBold,
                            modifier = Modifier.padding(horizontal = 5.dp, vertical = 1.5.dp)
                        )
                    }
                }
            }

            // ── Stage Info ──
            Text(
                text = stageInfo,
                color = Color.White.copy(alpha = 0.55f),
                fontSize = 10.sp,
                maxLines = 1,
                overflow = TextOverflow.Ellipsis
            )

            Spacer(modifier = Modifier.height(2.dp))

            // ── Teams + VS + Scores ──
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceEvenly,
                verticalAlignment = Alignment.CenterVertically
            ) {
                TeamColumn(
                    shortName = teamA.take(2).uppercase(),
                    fullName = teamA,
                    score = scoreA,
                    avatarColor = accentLight
                )

                Text(
                    text = "VS",
                    color = Color.White.copy(alpha = 0.4f),
                    fontSize = 12.sp,
                    fontWeight = FontWeight.ExtraBold,
                    modifier = Modifier.padding(horizontal = 8.dp)
                )

                TeamColumn(
                    shortName = teamB.take(2).uppercase(),
                    fullName = teamB,
                    score = scoreB,
                    avatarColor = accentMid
                )
            }
        }
    }
}

@Composable
private fun RowScope.TeamColumn(
    shortName: String,
    fullName: String,
    score: String,
    avatarColor: Color
) {
    Column(
        horizontalAlignment = Alignment.CenterHorizontally,
        modifier = Modifier.weight(1f)
    ) {
        Box(
            modifier = Modifier
                .size(36.dp)
                .clip(CircleShape)
                .background(avatarColor.copy(alpha = 0.2f))
                .border(1.dp, avatarColor.copy(alpha = 0.35f), CircleShape),
            contentAlignment = Alignment.Center
        ) {
            Text(
                text = shortName,
                color = avatarColor,
                fontSize = 13.sp,
                fontWeight = FontWeight.Bold
            )
        }

        Spacer(modifier = Modifier.height(4.dp))

        Text(
            text = fullName,
            color = Color.White,
            fontSize = 11.sp,
            fontWeight = FontWeight.SemiBold,
            maxLines = 1,
            overflow = TextOverflow.Ellipsis,
            textAlign = TextAlign.Center,
            modifier = Modifier.fillMaxWidth()
        )

        Spacer(modifier = Modifier.height(1.dp))

        Text(
            text = score,
            color = avatarColor,
            fontSize = 11.sp,
            fontWeight = FontWeight.Bold,
            textAlign = TextAlign.Center
        )
    }
}
