package com.devwithguru.cricket.ui.screens

import android.widget.Toast
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Build
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import kotlinx.coroutines.delay

data class EditableDelivery(
    val id: Int,
    val overIndex: Int,
    val ballIndex: Int,
    val bowler: String,
    val batter: String,
    val runs: Int,
    val extraType: String, // "None", "Wide", "No-Ball", "Bye", "Leg-Bye"
    val wicketInfo: String // "None" or dismissal description
)

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun MatchEditorScreen(
    matchId: String,
    onNavigateBack: () -> Unit
) {
    val context = LocalContext.current

    // Historical delivery logs in local state
    var deliveries by remember {
        mutableStateOf(
            listOf(
                EditableDelivery(1, 1, 1, "Shaheen Afridi", "Ahmed Ali", 1, "None", "None"),
                EditableDelivery(2, 1, 2, "Shaheen Afridi", "Bilal Butt", 4, "None", "None"),
                EditableDelivery(3, 1, 3, "Shaheen Afridi", "Bilal Butt", 0, "None", "Caught Shadab"),
                EditableDelivery(4, 1, 4, "Shaheen Afridi", "Salman Ahmed", 0, "Wide", "None"),
                EditableDelivery(5, 1, 5, "Shaheen Afridi", "Salman Ahmed", 2, "None", "None"),
                EditableDelivery(6, 1, 6, "Shaheen Afridi", "Salman Ahmed", 6, "None", "None"),
                EditableDelivery(7, 2, 1, "Naseem Shah", "Imran Khan", 1, "None", "None"),
                EditableDelivery(8, 2, 2, "Naseem Shah", "Salman Ahmed", 0, "None", "Bowled Naseem")
            )
        )
    }

    var editingDelivery by remember { mutableStateOf<EditableDelivery?>(null) }
    var isRebuilding by remember { mutableStateOf(false) }

    // Simulating stats rebuild
    if (isRebuilding) {
        AlertDialog(
            onDismissRequest = {},
            confirmButton = {},
            title = { Text("Rebuilding Scorecard Stats", color = MaterialTheme.colorScheme.onSurface, fontSize = 16.sp, fontWeight = FontWeight.Bold) },
            text = {
                Column(
                    modifier = Modifier.fillMaxWidth().padding(vertical = 12.dp),
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    CircularProgressIndicator(color = MaterialTheme.colorScheme.primary)
                    Text(
                        text = "Recalculating over metrics, partnership statistics, and player averages...",
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        fontSize = 12.sp,
                        lineHeight = 16.sp
                    )
                }
            },
            containerColor = MaterialTheme.colorScheme.surfaceVariant,
            shape = RoundedCornerShape(16.dp)
        )

        LaunchedEffect(Unit) {
            delay(1500)
            isRebuilding = false
            Toast.makeText(context, "Scorecard stats recalculated successfully!", Toast.LENGTH_SHORT).show()
        }
    }

    // Editing Dialog Modal
    editingDelivery?.let { delivery ->
        var editRuns by remember { mutableStateOf(delivery.runs) }
        var editExtraType by remember { mutableStateOf(delivery.extraType) }
        var editWicketInfo by remember { mutableStateOf(delivery.wicketInfo) }

        var showExtraDropdown by remember { mutableStateOf(false) }
        var showWicketDropdown by remember { mutableStateOf(false) }

        val extraTypes = listOf("None", "Wide", "No-Ball", "Bye", "Leg-Bye")
        val wicketTypes = listOf("None", "Bowled", "Caught Shadab", "LBW Naseem", "Run Out Bilal", "Stumped Rizwan")

        AlertDialog(
            onDismissRequest = { editingDelivery = null },
            title = {
                Text(
                    text = "Edit Delivery - Over ${delivery.overIndex}.${delivery.ballIndex}",
                    color = MaterialTheme.colorScheme.onSurface,
                    fontSize = 16.sp,
                    fontWeight = FontWeight.Bold
                )
            },
            text = {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(vertical = 8.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Batter & Bowler Info info panel
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .clip(RoundedCornerShape(8.dp))
                            .background(MaterialTheme.colorScheme.onSurface.copy(alpha = 0.03f))
                            .padding(10.dp)
                    ) {
                        Text(
                            text = "${delivery.bowler} to ${delivery.batter}",
                            color = MaterialTheme.colorScheme.onSurfaceVariant,
                            fontSize = 11.sp
                        )
                    }

                    // Runs scored selector row
                    Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                        Text(text = "Runs Scored", color = MaterialTheme.colorScheme.primary, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(6.dp)
                        ) {
                            (0..6).forEach { run ->
                                Box(
                                    modifier = Modifier
                                        .size(32.dp)
                                        .clip(RoundedCornerShape(6.dp))
                                        .background(
                                            if (editRuns == run) MaterialTheme.colorScheme.primary else MaterialTheme.colorScheme.onSurface.copy(alpha = 0.05f)
                                        )
                                        .clickable { editRuns = run },
                                    contentAlignment = Alignment.Center
                                ) {
                                    Text(
                                        text = "$run",
                                        color = if (editRuns == run) MaterialTheme.colorScheme.onPrimary else MaterialTheme.colorScheme.onBackground,
                                        fontWeight = FontWeight.Bold,
                                        fontSize = 12.sp
                                    )
                                }
                            }
                        }
                    }

                    // Extras selector field
                    Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                        Text(text = "Extra Type", color = MaterialTheme.colorScheme.primary, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clip(RoundedCornerShape(8.dp))
                                .background(MaterialTheme.colorScheme.onSurface.copy(alpha = 0.05f))
                                .border(1.dp, MaterialTheme.colorScheme.onSurface.copy(alpha = 0.1f), RoundedCornerShape(8.dp))
                                .clickable { showExtraDropdown = true }
                                .padding(12.dp)
                        ) {
                            Text(text = editExtraType, color = MaterialTheme.colorScheme.onSurface, fontSize = 13.sp)
                            DropdownMenu(
                                expanded = showExtraDropdown,
                                onDismissRequest = { showExtraDropdown = false },
                                modifier = Modifier.background(MaterialTheme.colorScheme.surfaceVariant)
                            ) {
                                extraTypes.forEach { type ->
                                    DropdownMenuItem(
                                        text = { Text(text = type, color = MaterialTheme.colorScheme.onSurface) },
                                        onClick = {
                                            editExtraType = type
                                            showExtraDropdown = false
                                        }
                                    )
                                }
                            }
                        }
                    }

                    // Wicket outcome selector field
                    Column(verticalArrangement = Arrangement.spacedBy(4.dp)) {
                        Text(text = "Wicket Outcome", color = MaterialTheme.colorScheme.primary, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .clip(RoundedCornerShape(8.dp))
                                .background(MaterialTheme.colorScheme.onSurface.copy(alpha = 0.05f))
                                .border(1.dp, MaterialTheme.colorScheme.onSurface.copy(alpha = 0.1f), RoundedCornerShape(8.dp))
                                .clickable { showWicketDropdown = true }
                                .padding(12.dp)
                        ) {
                            Text(text = editWicketInfo, color = MaterialTheme.colorScheme.onSurface, fontSize = 13.sp)
                            DropdownMenu(
                                expanded = showWicketDropdown,
                                onDismissRequest = { showWicketDropdown = false },
                                modifier = Modifier.background(MaterialTheme.colorScheme.surfaceVariant)
                            ) {
                                wicketTypes.forEach { type ->
                                    DropdownMenuItem(
                                        text = { Text(text = type, color = MaterialTheme.colorScheme.onSurface) },
                                        onClick = {
                                            editWicketInfo = type
                                            showWicketDropdown = false
                                        }
                                    )
                                }
                            }
                        }
                    }
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        deliveries = deliveries.map {
                            if (it.id == delivery.id) {
                                it.copy(runs = editRuns, extraType = editExtraType, wicketInfo = editWicketInfo)
                            } else {
                                it
                            }
                        }
                        editingDelivery = null
                        Toast.makeText(context, "Log updated", Toast.LENGTH_SHORT).show()
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.primary)
                ) {
                    Text("Save Changes", color = MaterialTheme.colorScheme.onPrimary)
                }
            },
            dismissButton = {
                TextButton(onClick = { editingDelivery = null }) {
                    Text("Cancel", color = MaterialTheme.colorScheme.primary)
                }
            },
            containerColor = MaterialTheme.colorScheme.surfaceVariant,
            shape = RoundedCornerShape(16.dp)
        )
    }

    Scaffold(
        topBar = {
            CenterAlignedTopAppBar(
                title = {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(
                            text = "Match Editor",
                            fontSize = 16.sp,
                            fontWeight = FontWeight.Bold,
                            color = MaterialTheme.colorScheme.onSurface
                        )
                        Text(
                            text = "Ali Panthers vs Islamabad Blasters",
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
                            contentDescription = "Back",
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

            Column(modifier = Modifier.fillMaxSize()) {
                // Info Banner
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(MaterialTheme.colorScheme.surfaceVariant)
                        .padding(vertical = 10.dp, horizontal = 16.dp)
                ) {
                    Text(
                        text = "Edit recorded ball events below. Remember to click Rebuild Stats to apply changes to scorecard averages.",
                        color = MaterialTheme.colorScheme.onSurfaceVariant,
                        fontSize = 11.sp,
                        lineHeight = 15.sp
                    )
                }

                // Deliveries List
                LazyColumn(
                    modifier = Modifier
                        .weight(1f)
                        .fillMaxWidth(),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    // Group deliveries by over index
                    val oversGrouped = deliveries.groupBy { it.overIndex }

                    oversGrouped.forEach { (overNum, balls) ->
                        item {
                            Text(
                                text = "Over $overNum",
                                color = MaterialTheme.colorScheme.primary,
                                fontSize = 12.sp,
                                fontWeight = FontWeight.Bold,
                                modifier = Modifier.padding(top = 8.dp)
                            )
                        }

                        items(balls) { delivery ->
                            DeliveryEditorItemCard(
                                delivery = delivery,
                                onEditClick = { editingDelivery = delivery }
                            )
                        }
                    }
                }

                // Sticky Bottom Action Bar
                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .background(MaterialTheme.colorScheme.surfaceVariant)
                        .padding(16.dp)
                ) {
                    Button(
                        onClick = { isRebuilding = true },
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(50.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.primary),
                        shape = RoundedCornerShape(12.dp)
                    ) {
                        Icon(Icons.Default.Build, null, tint = MaterialTheme.colorScheme.onPrimary)
                        Spacer(modifier = Modifier.width(8.dp))
                        Text(
                            text = "Rebuild Scorecard Stats",
                            color = MaterialTheme.colorScheme.onPrimary,
                            fontWeight = FontWeight.Bold,
                            fontSize = 14.sp
                        )
                    }
                }
            }
        }
    }
}

@Composable
fun DeliveryEditorItemCard(
    delivery: EditableDelivery,
    onEditClick: () -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .border(1.dp, MaterialTheme.colorScheme.onSurface.copy(alpha = 0.08f), RoundedCornerShape(12.dp)),
        colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.surfaceVariant),
        shape = RoundedCornerShape(12.dp)
    ) {
        Row(
            modifier = Modifier.padding(14.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
            Column(modifier = Modifier.weight(1f)) {
                val hasW = delivery.wicketInfo != "None"
                val isExtra = delivery.extraType != "None"

                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    Text(
                        text = "Ball ${delivery.overIndex}.${delivery.ballIndex}",
                        color = MaterialTheme.colorScheme.onSurface,
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Bold
                    )

                    // Render outcome pill
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(4.dp))
                            .background(
                                when {
                                    hasW -> MaterialTheme.colorScheme.error.copy(alpha = 0.15f)
                                    isExtra -> MaterialTheme.colorScheme.primary.copy(alpha = 0.15f)
                                    else -> MaterialTheme.colorScheme.onSurface.copy(alpha = 0.05f)
                                }
                            )
                            .padding(horizontal = 6.dp, vertical = 2.dp)
                    ) {
                        Text(
                            text = when {
                                hasW -> "W"
                                isExtra -> delivery.extraType
                                else -> "${delivery.runs} Run"
                            },
                            color = when {
                                hasW -> MaterialTheme.colorScheme.error
                                isExtra -> MaterialTheme.colorScheme.primary
                                else -> MaterialTheme.colorScheme.onSurface
                            },
                            fontSize = 9.sp,
                            fontWeight = FontWeight.Bold
                        )
                    }
                }
                Spacer(modifier = Modifier.height(4.dp))
                Text(
                    text = "${delivery.bowler} to ${delivery.batter}",
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                    fontSize = 11.sp
                )
                if (hasW) {
                    Text(
                        text = "Outcome: ${delivery.wicketInfo}",
                        color = MaterialTheme.colorScheme.error,
                        fontSize = 11.sp,
                        fontWeight = FontWeight.Medium
                    )
                }
            }

            IconButton(onClick = onEditClick) {
                Icon(
                    imageVector = Icons.Default.Edit,
                    contentDescription = "Edit Delivery log",
                    tint = MaterialTheme.colorScheme.primary,
                    modifier = Modifier.size(20.dp)
                )
            }
        }
    }
}
