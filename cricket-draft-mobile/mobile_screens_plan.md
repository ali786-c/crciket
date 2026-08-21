# STUMPS Cricket Platform: Mobile Screens & UI Blueprint (Refined)

This document maps the complete screen hierarchy and design flows for the Android Jetpack Compose mobile application, centered around a unified homepage, independent match scheduling, and a detailed tournament hub.

---

## 🏠 1. The Unified Homepage Layout (Central Hub)

The landing screen features a unified dashboard with quick actions at the bottom for creating resources:

```
+-----------------------------------------------------+
|  STUMPS logo / Search Bar          (Notif Icon)     |
+-----------------------------------------------------+
|  🔴 LIVE & RECENT MATCHES (Horizontal Scroll Row)     |
|  [ Team A vs Team B ]  [ Team C vs Team D (Live) ]   |
+-----------------------------------------------------+
|  👤 MY PROFILE QUICK ACCESS CARD                     |
|  Ahmed Ali (All-rounder) | Runs: 324 | Wkts: 14      |
|  *Click opens full Career Profile Screen*           |
+-----------------------------------------------------+
|  🏆 LIVE & ACTIVE TOURNAMENTS (Vertical List)       |
|  - Premier Cricket Cup 2026                         |
+-----------------------------------------------------+
|  ➕ QUICK ACTIONS BUTTONS                            |
|  [ + Create Match (Single) ]  [ + Create Tournament ]|
+-----------------------------------------------------+
|  📂 DIRECTORIES                                     |
|  [ My Tournaments ]  [ My Teams ]  [ Unified Search ]|
+-----------------------------------------------------+
```

---

## 📅 2. Screen Hierarchy & Modular Workflows

---

### 📂 Module A: Core Creation Flows (2 Screens)

#### Screen A.1: Create Single Match (Friendly Match)
*   **Purpose**: Start an independent match between two teams outside of any tournament.
*   **Form Inputs**:
    *   Home Team (Select from registry or type name).
    *   Away Team (Select from registry or type name).
    *   Overs per Innings (Numeric slider/input).
    *   Ball Type (Tennis vs. Leather).
    *   Match Date/Time.
*   **Follow-up Flow**: Directly transitions to the **Unified Match Scoring Flow** (Toss -> Lineup -> Scoring).

#### Screen A.2: Create Tournament Screen
*   **Purpose**: Create a new tournament.
*   **Form Inputs**:
    *   Tournament Name.
    *   City (Where the tournament is played).
    *   Season name/year (e.g. `2026`).
    *   Start Date (Calendar dialog picker).
    *   End Date (Calendar dialog picker).
    *   Ball Type (Segmented toggle: `Tennis` or `Leather`).
*   **Follow-up Flow**: Saves tournament and directs user to the newly created tournament dashboard.

---

### 🏆 Module B: "My Tournaments" & Tournament Hub (7 Screens)

#### Screen B.1: "My Tournaments" Directory
*   **Purpose**: Displays all tournaments owned/created by the logged-in user.
*   **UI Components**:
    *   Grid/List of tournament summary cards.
    *   Search and filter tabs.

---

#### 📌 The Tournament Dashboard Hub
Tapping any tournament in the directory opens a tabbed controller with **5 main tabs/screens**:

```
+-----------------------------------------------------------------+
|  Tournament: Premier Cricket Cup 2026                           |
+-----------------------------------------------------------------+
| [ Home ] [ Teams ] [ Matches ] [ Points Table ] [ Statistics ] |
+-----------------------------------------------------------------+
|                                                                 |
|                 (Active Tab Content Area)                       |
|                                                                 |
+-----------------------------------------------------------------+
```

#### Screen B.2: Tab 1 - Home / Overview Tab
*   **UI Components**:
    *   Tournament status banners (Registration, Ready, Live, Completed).
    *   *Recent Match Results*: Card list showing scores and outcomes of recently completed matches.
    *   *Top Performers Highlights*: Cards displaying the tournament's leading statistics:
        *   Top Batsman preview (player name, team logo, total runs).
        *   Top Bowler preview (player name, team logo, total wickets).
    *   *Honors Board (Tournament Conclusion Section)*: Renders at the bottom of the feed once the tournament is `completed`:
        *   🏆 Tournament Winner Team (Championship cup layout).
        *   🥈 Runner-up Team.
        *   🎖️ Player of the Tournament / Man of the Tournament (overall MVP points leader).
    *   News feed updates for this tournament.

#### Screen B.3: Tab 2 - Teams Directory Tab
*   **UI Components**:
    *   List of teams registered in the tournament.
    *   Tapping a team navigates to the **Detailed Team Screen** (Screen B.3.1).

#### Screen B.3.1: Detailed Team Screen
A tabbed container view representing a specific team, featuring **5 dedicated tabs**:

```
+-------------------------------------------------------------+
|  Team: Ali Panthers                                         |
+-------------------------------------------------------------+
| [ Home ] [ Players ] [ Matches ] [ Tournaments ] [ Stats ]  |
+-------------------------------------------------------------+
|                                                             |
|                 (Active Tab Content Area)                   |
|                                                             |
+-------------------------------------------------------------+
```

##### 1. Home / Overview Tab
*   **UI Components**:
    *   Team header card: Name, founded year, logo, owner, and overall win/loss record.
    *   *Top Performers*: Leading batsman and bowler of this team.
    *   *Schedule Preview*: Scrollable list of upcoming scheduled matches.

##### 2. Players (Squad) Tab
*   **UI Components**:
    *   List of players registered in the team, grouped by roles (Batters, Bowlers, All-rounders, Wicketkeepers).
    *   Tapping any player card opens their **Detailed Career Profile Screen** (Screen D.1).
    *   *Manage Squad Actions (Owner-Only)*:
        *   "Add Player" button and "Remove/Delete Player" button.
        *   **Security Gate**: These buttons are strictly visible **only to the user who created the tournament**. Guest/Player/Captain views will render in read-only format.

##### 3. Matches Tab
*   **UI Components**:
    *   Chronological feed of matches:
        *   *Scheduled*: Upcoming matches with opponent, date, time, and venue.
        *   *Completed*: Historical scorecards showing results and totals.

##### 4. Tournaments Tab
*   **UI Components**:
    *   Vertical list of all tournaments this team has participated in (active and past).

##### 5. Statistics Tab
*   **UI Components**:
    *   Deep team-specific statistics: Win percentage, toss analytics, average batting totals (batting first vs chasing), highest successfully chased total, and boundary credits counts.

#### Screen B.4: Tab 3 - Matches & Fixtures Tab
*   **UI Components**:
    *   Full list of fixtures categorized by status:
        *   *Scheduled Matches Section*: Opponent team cards, venue, scheduled date/time, and a "Start Match" button (Admins/Scorers only).
        *   *Completed Matches Section*: Past match cards displaying final runs, overs, and outcomes.
    *   *Match Click Action*: Tapping any match card navigates directly to the **Match Details Screen** (Module C).

#### Screen B.5: Tab 4 - Points Table Tab
*   **UI Components**:
    *   Ranked list of standings containing columns:
        *   `Pos` (Rank position).
        *   `Team` (Team name and logo).
        *   `P` (Played).
        *   `W` (Won).
        *   `L` (Lost).
        *   `T` (Tied).
        *   `NR` (No Result).
        *   `Pts` (Points).
        *   `NRR` (Net Run Rate).
    *   Visual highlights for top qualifying zones (e.g. green background for top 4 teams).

#### Screen B.6: Tab 5 - Statistics Tab
*   **UI Components**:
    *   *Batting Leaderboards*:
        *   **Most Runs**: Leading tournament run-scorers (player, team, matches, runs, average, strike rate).
        *   **Highest Scores**: Top individual batting scores in a single innings (runs, balls faced, opponent, strike rate).
        *   **Best Batting Average**: Highest runs-per-dismissal ratio (minimum 3 innings).
        *   **Best Batting Strike Rate**: Runs scored per 100 balls (minimum 30 balls faced).
        *   **Most Sixes (`6s`)**: Leaderboard of players with the most hit sixes.
        *   **Most Fours (`4s`)**: Leaderboard of players with the most hit fours.
        *   **Most 50s / 100s**: Count of half-centuries and centuries scored.
    *   *Bowling Leaderboards*:
        *   **Most Wickets**: Leading tournament wicket-takers (player, team, matches, wickets, average, economy).
        *   **Best Bowling Figures**: Top single-innings bowling spells (wickets/runs conceded, overs, opponent).
        *   **Best Bowling Average**: Lowest runs-conceded-per-wicket ratio (minimum 5 wickets taken).
        *   **Best Economy Rate**: Lowest runs conceded per over (minimum 6 overs bowled).
        *   **Best Bowling Strike Rate**: Lowest balls bowled per wicket taken (minimum 5 wickets).
        *   **Most Dot Balls**: Highest percentage of dot deliveries bowled.
        *   **Most Maidens**: Total counts of maiden overs bowled.
        *   **Most 5-Wicket Hauls**: Players who have taken 5 or more wickets in a single innings.
    *   *Fielding Leaderboards*:
        *   **Most Dismissals**: Combined catches, stumpings, and run-outs.
        *   **Most Catches**: Leading fielders by catches taken.
        *   **Most Stumpings**: Leading wicketkeepers by stumpings made.
        *   **Most Run-outs**: Leading fielders by run-out involvements.
    *   *Partnership Records*:
        *   **Highest Partnerships**: Top run partnerships (runs, batting pair, wicket position, match details).
    *   *Tournament MVP Table*:
        *   **MVP Leaderboard**: Overall player ratings sorted by total batting, bowling, and fielding MVP points.

#### Screen B.7: Screen - Draft Room Access (Optional Tab)
*   **UI Components**:
    *   If `has_draft` is enabled for this tournament, this tab accesses the **Live Draft Board** (snake schedules, active clocks, pick selectors).

---

### 🏏 Module C: Unified Match Scoring & Center Flow (4 Screens)
This flow applies to both independent friendly matches and tournament matches:

```
[ Toss & Lineups ] ---> [ Live Scorer Console ] ---> [ Match Center Scorecard ]
                              ^                             |
                              |                             v
                              +-------------------- [ Match Editor Panel ]
```

#### Screen C.1: Toss & Lineup Submission Screen
*   **Purpose**: Submit team rosters and toss choices.
*   **UI Components**:
    *   Roster lists to select playing XI from team squad.
    *   Toss Winner selection (Home/Away Team) and Toss Decision (Bat/Bowl).

#### Screen C.2: Live Scorer Console
*   **Purpose**: Chronological ball-by-ball delivery entries.
*   **UI Components**:
    *   Runs keyboard (`0`, `1`, `2`, `3`, `4`, `6`).
    *   Extras buttons (Wd, Nb, Byes, Lb) with custom extra penalty inputs.
    *   Wicket Panel: Dismissal types, dismissed batsman, and fielder credit selectors.
    *   Undo last delivery button.
    *   Innings complete manager overlay.

#### Screen C.3: Match Center (Derived Scorecard & Analytics)
A tabbed container view representing a live or completed match, featuring **4 main tabs**:

```
+-------------------------------------------------------------+
|  Match: Team A vs Team B                                    |
+-------------------------------------------------------------+
| [ Summary ] [ Scorecard ] [ Stats ] [ Super Stars ]         |
+-------------------------------------------------------------+
|                                                             |
|                 (Active Tab Content Area)                   |
|                                                             |
+-------------------------------------------------------------+
```

##### 1. Summary Tab
*   **UI Components**:
    *   *Team Banners & Live Score*: Displays playing team names and active scorelines (runs/wickets, e.g., **124/4**) in a large, **bold** font.
    *   *Over & Run Rate Panel*:
        *   Extras count (Total Wide, No-ball, Byes, Leg-byes).
        *   Overs bowled progress (format: `current_overs / total_overs`).
        *   Current Run Rate (CRR).
    *   *Match Indicators*:
        *   Projected Score (calculated based on current run rate).
        *   Current Partnership (runs scored off balls faced by the active pair).
    *   *Active Batsmen Duo (Table)*:
        *   Two rows showing striker and non-striker.
        *   Striker highlighted with an asterisk (`*`).
        *   Columns: `Batsman`, `R` (Runs), `B` (Balls), `4s` (Fours), `6s` (Sixes), `SR` (Strike Rate).
    *   *Active Bowler Card (Table)*:
        *   Active bowler metrics.
        *   Columns: `Bowler`, `O` (Overs), `M` (Maidens), `R` (Runs), `W` (Wickets), `Econ` (Economy).
        *   *Shorthand Rule*: Headers MUST be labeled as **R** (Runs), **O** (Overs), and **M** (Maidens) to save screen space.
    *   *⚠️ Live Scoring Keyboard Panel (Conditional - Admin/Scorer Only)*:
        *   Anchored at the bottom of the Summary tab, visible **only to the match owner/admin**. For spectators, this keyboard panel is hidden.
        *   *Runs Keys*: `Dot (0)`, `1`, `2`, `3`, `4`, `5`, `6`.
        *   *Extras Keys*: `Wide`, `No-Ball`, `Leg-Bye`, `Bye`.
        *   *Actions Keys*: `Wicket` (W), `Undo` (U).
        *   **Wicket (Out) Workflow**: Tapping `Wicket` opens a dialog to select: Dismissal Type, dismissed batsman, and fielder (if caught/run-out). Saving the wicket freezes the keyboard and triggers a fullscreen overlay: **"Select Next Batsman"** showing all remaining un-dismissed players from the team's Playing XI.
        *   **Over Transition Workflow**: When the number of legal deliveries bowled in the over reaches the configured limit (default: 6), the keyboard locks and displays a bottom-sheet: **"Select Next Bowler"** listing all players on the bowling team (excluding the bowler who just completed their over).
        *   **Undo Workflow**: Tapping the `Undo` button prompts: "Undo latest ball?". On confirmation, dispatches rollback action to the server, restoring batsman/bowler states and recalculating scores.

##### 2. Scorecard Tab
*   **UI Components**:
    *   *Innings Switcher*: Tab selector toggle showing both team names.
    *   *Batting Card Section*:
        *   Full list of batsmen in the playing lineup.
        *   For each batsman: Name, dismissal state description (e.g. `c Ali b Khan`), `R`, `B`, `4s`, `6s`, `SR`.
        *   Subtotal row for Extras and Total Team Score (with wickets and overs count).
    *   *Bowling Card Section*:
        *   Complete bowling roster details at the bottom of the innings card.
        *   Columns: `Bowler`, `O`, `M`, `R`, `W`, `Econ`.

##### 3. Stats Tab
*   **UI Components**:
    *   *Wagon Wheel Canvas*: Angle vector lines mapping run directions from the center of the pitch.
    *   *Over Comparison*: Over-by-over run progression comparisons (line graph).
    *   *Runs Comparison*: Pie chart/bar breakdown of runs from boundaries (4s/6s) vs. running (1s/2s/3s) vs. extras.

##### 4. Super Stars Tab (MVP Performers)
*   **UI Components**:
    *   Grid displaying the **Top 4 Players of the Match** (Super Stars) selected dynamically based on calculated MVP points.
    *   *Dynamic Logic*: Extracts the 4 players with the highest MVP Points (runs + boundary bonuses + wickets/maidens + catches/stumpings).
    *   Each star's card displays: Player name, team logo, total MVP points, and key performance summary (e.g., `45 Runs (30b) & 2/14 (3 ov)`).

#### Screen C.4: Match Editor & Post-Match Corrections Screen
*   **Purpose**: Admin screen to edit historic delivery entries.
*   **UI Components**:
    *   List of recorded deliveries.
    *   Edit form to modify runs, batsman names, extras, or wicket data on a specific delivery.
    *   Rebuild stats execution button (triggers transaction-safe scorecard recalculations on backend).

---

### 👤 Module D: User Profiles & Discovery (2 Screens)

#### Screen D.1: Detailed Career Profile Screen
A tabbed container view representing a player's profile and career details, featuring **5 dedicated tabs**:

```
+-------------------------------------------------------------+
|  Player: Ahmed Ali (Right-hand bat, Right-arm fast)         |
+-------------------------------------------------------------+
| [ Overview ] [ Stats ] [ Matches ] [ Teams ] [ Tournaments ]|
+-------------------------------------------------------------+
|                                                             |
|                 (Active Tab Content Area)                   |
|                                                             |
+-------------------------------------------------------------+
```

##### 1. Overview Tab
*   **UI Components**:
    *   Player card details: Photo, playing style, city, and bio.
    *   *Batting Card*: Runs, Average, and High Score (HS).
    *   *Bowling Card*: Wickets, Average, and Best bowling spell.
    *   *Fielding Card*: Catches (Catches), Stumpings (Stumps), and Run-outs.

##### 2. Stats Tab
*   **UI Components**:
    *   *Full Batting Statistics*: Matches, Innings, Runs, Average, Strike Rate, HS, 50s, 100s.
    *   *Full Bowling Statistics*: Matches, Overs, Runs conceded, Wickets, Average, Economy, Best spelling, 5W hauls.
    *   *Full Fielding Statistics*: Catches, Stumpings, Run-outs.

##### 3. Matches Tab
*   **UI Components**:
    *   List of all matches this player has played, displaying date, opponent team, and player contributions (runs scored and wickets taken).

##### 4. Teams Tab
*   **UI Components**:
    *   List of all teams the player is currently registered under or has played for in the past.

##### 5. Tournaments Tab
*   **UI Components**:
    *   List of tournaments the player has participated in.

#### Screen D.2: Unified Global Search
*   **Purpose**: Single search input mapping matching players, teams, matches, and tournaments.
