# Agent Customization Rules

## UI Responsiveness Rule
- **No Hardcoded Horizontal Sizes**: Avoid using hardcoded margins, paddings, or fixed widths for elements displayed side-by-side in rows.
- **Equal Weighting**: Always use Compose weight modifiers (e.g., `Modifier.weight(1f)`) to partition the screen space evenly among side-by-side components.
- **Responsive Wrap**: Design labels and values inside layout columns to support wrapping vertically rather than truncating or clipping horizontally on small mobile screens.
