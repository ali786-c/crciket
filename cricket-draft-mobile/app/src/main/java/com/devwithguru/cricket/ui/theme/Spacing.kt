package com.devwithguru.cricket.ui.theme

import androidx.compose.runtime.Composable
import androidx.compose.runtime.ReadOnlyComposable
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

// ─── Spacing Tokens ─────────────────────────────────────────
object Spacing {
    val xs = 4.dp
    val sm = 8.dp
    val md = 12.dp
    val lg = 16.dp
    val xl = 20.dp
    val xxl = 24.dp
    val xxxl = 32.dp
    val huge = 48.dp
}

// ─── Corner Radius Tokens ───────────────────────────────────
object CornerRadius {
    val sm = 8.dp
    val md = 12.dp
    val lg = 16.dp
    val xl = 20.dp
    val full = 999.dp
}

// ─── Font Size Tokens ───────────────────────────────────────
object FontSize {
    val caption = 10.sp
    val small = 11.sp
    val body = 13.sp
    val bodyLarge = 14.sp
    val subtitle = 16.sp
    val title = 18.sp
    val heading = 22.sp
    val display = 28.sp
}

// ─── Responsive Spacing ─────────────────────────────────────
object ResponsiveSpacing {
    /**
     * Returns a Dp value that scales with screen size.
     * small=compact phone, medium=standard, large=big phone/tablet
     */
    @Composable
    @ReadOnlyComposable
    fun horizontalPadding(): Dp = LocalScreenConfig.current.horizontalPadding

    @Composable
    @ReadOnlyComposable
    fun verticalPadding(): Dp = LocalScreenConfig.current.verticalPadding

    @Composable
    @ReadOnlyComposable
    fun cardPadding(): Dp = LocalScreenConfig.current.cardPadding

    @Composable
    @ReadOnlyComposable
    fun sectionSpacing(): Dp = LocalScreenConfig.current.sectionSpacing

    @Composable
    @ReadOnlyComposable
    fun avatarSize(): Dp = LocalScreenConfig.current.avatarSize

    @Composable
    @ReadOnlyComposable
    fun iconSize(): Dp = LocalScreenConfig.current.iconSize
}
