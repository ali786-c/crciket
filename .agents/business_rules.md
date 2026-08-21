# Cricket Draft App — Business Rules & Logic Reference

## 1. Player System

### 1.1 Player Registration & Profiles
- **Every player added to any team automatically gets a profile** in `PlayerRegistry`.
- `PlayerRegistry` is the single source of truth for all player data.
- `PlayerProfileScreen` reads player data from `PlayerRegistry.findById(id)`.
- Both manually added and registered players get profiles.

### 1.2 Adding Players to Teams
- Players can be added via two methods in the Lineup screen:
  1. **Search by Player ID**: If a player already has a registered account, enter their ID (e.g. `h1`, `a2`, `p5`) to find and add them.
  2. **New Player (Manual)**: Enter name + select role (Batter, Bowler, All-rounder, Wicketkeeper) — auto-registers in `PlayerRegistry`.
- Newly added players are auto-selected in the lineup.

### 1.3 Player Roles
- Batter
- Bowler
- All-rounder
- Wicketkeeper

---

## 2. Match Creation Flow

### 2.1 Create Match Screen Fields (all required)
| Field | Type | Validation |
|-------|------|-----------|
| Home Team | Select from existing or create new | Required, cannot be same as Away |
| Away Team | Select from existing or create new | Required |
| Venue | Free text | Cannot be blank |
| Date | Date picker | Required |
| Time | Time picker | Required |
| Overs | Number input + dropdown suggestions | Must be > 0 |
| Match Type | Dropdown: T10, T20, One Day, Test | Required |
| Ball Type | Dropdown: Tennis, Leather | Required |
| Wickets | Number input + dropdown suggestions | Must be > 0 |

### 2.2 Overs & Wickets Inputs
- **Only accept numbers** (KeyboardType.Number + digit filter).
- Text labels (Overs, Wickets) are shown **above** the field, not inside it.
- Dropdown arrow provides quick selection of common values.
- Custom values can be typed manually.

### 2.3 Two Actions on Create Match
1. **SAVE FIXTURE**: Saves to `FixtureStore` with status "Scheduled". Visible in Profile → Matches tab. User can start later.
2. **START MATCH**: Proceeds directly to Toss → Lineup → Live Scorer.

---

## 3. Match Flow (Navigation)

```
Create Match → Toss Screen → Lineup Screen → Live Scorer
```

### 3.1 Toss Screen (Separate)
- Step 1: Select which team won the toss
- Step 2: Choose Bat or Bowl
- Step 3: Coin flip animation (3D rotationX, 2.5s, gold gradient coin)
- Step 4: Result reveal with spring animation → "PROCEED TO LINEUP"

### 3.2 Lineup Screen
- Shows toss result banner at top
- Two team tabs with player count
- "ADD PLAYER" button opens dialog (Search by ID / New Player)
- Minimum 2 players per team to start match
- Players can be selected/deselected via checkbox

---

## 4. Scheduled Fixtures

- Stored in `FixtureStore.fixtures` (in-memory `mutableStateListOf`)
- Shown in **Profile → Matches tab** with orange "Scheduled" badge
- Each fixture shows: teams, date/time, overs, type, ball, wickets, venue
- "START MATCH" button on each fixture → goes to Toss screen
- Status changes to "Live" when started

---

## 5. UI/Design Rules

### 5.1 Colors
- **NEVER copy colors from screenshots** — screenshots are for layout/flow reference only.
- Always use the app's own theme colors: `MaterialTheme.colorScheme.primary` (lime green), etc.
- Text on primary buttons should be **Color.Black** for contrast.

### 5.2 Field Labels
- Every input field must have a **permanent visible label** above it (11.sp, 50% white opacity).
- Labels stay visible regardless of whether the field has a value.

### 5.3 Form Validation
- All fields must be validated before any action (SAVE FIXTURE / START MATCH).
- Error messages shown as red text at the top of the form.
- Validation is sequential (first empty field triggers error).

### 5.4 Icons — No Emojis
- **NEVER use emojis** (🏏⚾🪙📍 etc.) in the app UI. Emojis are not professional.
- Always use **Material Icons** (`Icons.Default.*`) instead.
- Examples: `Autorenew` for coin/toss, `Shield` for bat, `FitnessCenter` for bowl, `LocationOn` for venue, `Schedule` for time.

### 5.5 Mobile Responsiveness (Screen Compatibility)
- **No Hardcoded Horizontal Sizes**: Avoid using hardcoded margins, paddings, or fixed widths for elements displayed side-by-side in rows.
- **Equal Weighting**: Always use Compose weight modifiers (e.g., `Modifier.weight(1f)`) to partition the screen space evenly among side-by-side components.
- **Responsive Wrap**: Design labels and values inside layout columns to support wrapping vertically rather than truncating or clipping horizontally on small mobile screens.

---

## 6. Team System

### 6.1 Creating Teams
- Teams can be created inline during match creation via the "Create New Team" dialog.
- New team only needs: Team Name + Location.
- Created teams appear in the team selection list immediately.

### 6.2 Team Selection (Create Match)
- Shows searchable list of existing teams.
- Search works globally across all teams.
- "Create New Team" option available at the bottom.

---

## 7. Data Architecture (In-Memory Singletons)

| Singleton | Purpose | File |
|-----------|---------|------|
| `PlayerRegistry` | All players, search by ID, auto-profile | `PlayerRegistry.kt` |
| `FixtureStore` | Scheduled/saved fixtures | `FixtureStore.kt` |

> **Note**: Currently all data is in-memory. When backend is added, these singletons should be replaced with proper Repository pattern + Room/API calls.

---

## 8. Architecture Rules (MVVM Guidance)

### 8.1 Model-View-ViewModel (MVVM)
- **Separate Logic from UI**: Composable screens should only be responsible for rendering the UI and forwarding user events. All business logic, scoring calculations, and state changes must reside inside dedicated **ViewModels** (inheriting from `androidx.lifecycle.ViewModel`).
- **Observable State**: Expose UI state from ViewModels using Compose state mechanisms (`mutableStateOf`) or reactive streams (`StateFlow`).
- **Single Source of Truth**: UI screens must observe state properties from their respective ViewModel and call public ViewModel functions to request updates. Do not manage scoring engine mathematical states locally within Compose elements.

