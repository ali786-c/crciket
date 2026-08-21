package com.devwithguru.cricket.ui.screens

import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

@Composable
fun TeamStatsTab() {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Text(
            text = "Detailed Team Statistics",
            color = MaterialTheme.colorScheme.primary,
            fontSize = 14.sp,
            fontWeight = FontWeight.Bold
        )

        Card(
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, Color.White.copy(alpha = 0.05f), RoundedCornerShape(12.dp)),
            colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant),
            shape = RoundedCornerShape(12.dp)
        ) {
            Column(
                modifier = Modifier.padding(14.dp),
                verticalArrangement = Arrangement.spacedBy(14.dp)
            ) {
                StatMetricRow(label = "Overall Win Rate", value = "75% (15 W / 5 L)")
                HorizontalDivider(color = Color.White.copy(alpha = 0.05f))
                StatMetricRow(label = "Toss Win Rate", value = "60%")
                HorizontalDivider(color = Color.White.copy(alpha = 0.05f))
                StatMetricRow(label = "Avg. Runs Batting First", value = "162.4 runs")
                HorizontalDivider(color = Color.White.copy(alpha = 0.05f))
                StatMetricRow(label = "Highest Chased Total", value = "185/4 vs Kings")
                HorizontalDivider(color = Color.White.copy(alpha = 0.05f))
                StatMetricRow(label = "Boundaries Count", value = "112 Fours / 45 Sixes")
            }
        }
    }
}

@Composable
fun StatMetricRow(label: String, value: String) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(text = label, color = MaterialTheme.colorScheme.onSurfaceVariant, fontSize = 13.sp)
        Text(text = value, color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
    }
}
