package com.devwithguru.cricket.ui.components

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.KeyboardArrowRight
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.devwithguru.cricket.ui.theme.*

// ─── CricketCard ────────────────────────────────────────────
@Composable
fun CricketCard(
    modifier: Modifier = Modifier,
    onClick: (() -> Unit)? = null,
    content: @Composable ColumnScope.() -> Unit
) {
    val cardModifier = modifier
        .fillMaxWidth()
        .clip(RoundedCornerShape(CornerRadius.lg))
        .then(
            if (onClick != null) Modifier.clickable { onClick() } else Modifier
        )

    Card(
        modifier = cardModifier,
        colors = CardDefaults.cardColors(
            containerColor = MaterialTheme.colorScheme.surface
        ),
        elevation = CardDefaults.cardElevation(defaultElevation = 0.dp),
        shape = RoundedCornerShape(CornerRadius.lg),
        border = CardDefaults.outlinedCardBorder().takeIf { false }
    ) {
        Column(
            modifier = Modifier.padding(Spacing.lg),
            content = content
        )
    }
}

// ─── CricketCardOutlined ────────────────────────────────────
@Composable
fun CricketCardOutlined(
    modifier: Modifier = Modifier,
    onClick: (() -> Unit)? = null,
    content: @Composable ColumnScope.() -> Unit
) {
    val cardModifier = modifier
        .fillMaxWidth()
        .clip(RoundedCornerShape(CornerRadius.lg))
        .border(
            width = 1.dp,
            color = MaterialTheme.colorScheme.outline,
            shape = RoundedCornerShape(CornerRadius.lg)
        )
        .background(MaterialTheme.colorScheme.surface)
        .then(
            if (onClick != null) Modifier.clickable { onClick() } else Modifier
        )

    Column(
        modifier = cardModifier.padding(Spacing.lg),
        content = content
    )
}

// ─── Status Badge ───────────────────────────────────────────
@Composable
fun StatusBadge(
    status: String,
    modifier: Modifier = Modifier
) {
    val (bgColor, textColor) = when (status.lowercase()) {
        "live", "active" -> StatusLiveBg to StatusLive
        "completed" -> StatusCompletedBg to StatusCompleted
        "upcoming", "registration", "ready", "draft" -> StatusUpcomingBg to StatusUpcoming
        "cancelled" -> StatusCancelledBg to StatusCancelled
        else -> MaterialTheme.colorScheme.surfaceVariant to MaterialTheme.colorScheme.onSurfaceVariant
    }

    Surface(
        modifier = modifier,
        shape = RoundedCornerShape(CornerRadius.sm),
        color = bgColor
    ) {
        Text(
            text = status.uppercase(),
            color = textColor,
            fontSize = FontSize.caption,
            fontWeight = FontWeight.Bold,
            modifier = Modifier.padding(horizontal = 8.dp, vertical = 3.dp)
        )
    }
}

// ─── Live Badge ─────────────────────────────────────────────
@Composable
fun LiveBadge(modifier: Modifier = Modifier) {
    Surface(
        modifier = modifier,
        shape = RoundedCornerShape(4.dp),
        color = StatusLive
    ) {
        Row(
            modifier = Modifier.padding(horizontal = 6.dp, vertical = 2.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(4.dp)
        ) {
            Box(
                modifier = Modifier
                    .size(5.dp)
                    .clip(CircleShape)
                    .background(Color.White)
            )
            Text(
                text = "LIVE",
                color = MaterialTheme.colorScheme.onSurface,
                fontSize = 9.sp,
                fontWeight = FontWeight.ExtraBold
            )
        }
    }
}

// ─── Section Header ─────────────────────────────────────────
@Composable
fun SectionHeader(
    title: String,
    actionText: String? = null,
    onActionClick: (() -> Unit)? = null,
    modifier: Modifier = Modifier
) {
    Row(
        modifier = modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = title,
            color = MaterialTheme.colorScheme.onBackground,
            fontSize = FontSize.title,
            fontWeight = FontWeight.SemiBold,
            letterSpacing = (-0.3).sp
        )

        if (actionText != null && onActionClick != null) {
            Row(
                modifier = Modifier.clickable { onActionClick() },
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.spacedBy(2.dp)
            ) {
                Text(
                    text = actionText,
                    color = MaterialTheme.colorScheme.primary,
                    fontSize = FontSize.body,
                    fontWeight = FontWeight.Medium
                )
                Icon(
                    imageVector = Icons.Default.KeyboardArrowRight,
                    contentDescription = null,
                    tint = MaterialTheme.colorScheme.primary,
                    modifier = Modifier.size(16.dp)
                )
            }
        }
    }
}

// ─── Team Avatar ────────────────────────────────────────────
@Composable
fun TeamAvatar(
    shortName: String,
    size: Dp = 44.dp,
    color: Color = MaterialTheme.colorScheme.primary,
    modifier: Modifier = Modifier
) {
    Box(
        modifier = modifier
            .size(size)
            .clip(CircleShape)
            .background(color.copy(alpha = 0.15f))
            .border(1.5.dp, color.copy(alpha = 0.3f), CircleShape),
        contentAlignment = Alignment.Center
    ) {
        Text(
            text = shortName.take(2).uppercase(),
            color = color,
            fontSize = (size.value * 0.35).sp,
            fontWeight = FontWeight.Bold
        )
    }
}

// ─── Info Row ───────────────────────────────────────────────
@Composable
fun InfoRow(
    icon: ImageVector,
    label: String,
    value: String,
    modifier: Modifier = Modifier
) {
    Row(
        modifier = modifier.fillMaxWidth(),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.spacedBy(Spacing.sm)
    ) {
        Icon(
            imageVector = icon,
            contentDescription = null,
            tint = MaterialTheme.colorScheme.onSurfaceVariant,
            modifier = Modifier.size(16.dp)
        )
        Text(
            text = label,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
            fontSize = FontSize.body,
            modifier = Modifier.weight(1f)
        )
        Text(
            text = value,
            color = MaterialTheme.colorScheme.onSurface,
            fontSize = FontSize.body,
            fontWeight = FontWeight.Medium
        )
    }
}

// ─── Score Display ──────────────────────────────────────────
@Composable
fun ScoreDisplay(
    runs: Int,
    wickets: Int,
    overs: String,
    modifier: Modifier = Modifier,
    color: Color = MaterialTheme.colorScheme.primary
) {
    Text(
        text = "$runs-$wickets ($overs)",
        color = color,
        fontSize = FontSize.bodyLarge,
        fontWeight = FontWeight.Bold,
        modifier = modifier
    )
}

// ─── Empty State ────────────────────────────────────────────
@Composable
fun EmptyState(
    icon: ImageVector,
    title: String,
    subtitle: String,
    modifier: Modifier = Modifier
) {
    Column(
        modifier = modifier
            .fillMaxWidth()
            .padding(Spacing.xxxl),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.spacedBy(Spacing.sm)
    ) {
        Icon(
            imageVector = icon,
            contentDescription = null,
            tint = MaterialTheme.colorScheme.onSurfaceVariant.copy(alpha = 0.5f),
            modifier = Modifier.size(48.dp)
        )
        Text(
            text = title,
            color = MaterialTheme.colorScheme.onSurface,
            fontSize = FontSize.subtitle,
            fontWeight = FontWeight.Medium
        )
        Text(
            text = subtitle,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
            fontSize = FontSize.body,
            maxLines = 2,
            overflow = TextOverflow.Ellipsis
        )
    }
}
