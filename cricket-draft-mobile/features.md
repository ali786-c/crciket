# STUMPS Cricket Platform: Exhaustive Mobile & Backend Feature Specifications

This document defines the deep, detailed specifications for all 22+ feature domains of the mobile and backend platform. It serves as the primary technical specification for developers.

---

## 🗺️ System Roadmap & Complete Features Index

1. [🏏 Live Cricket Scoring (Online & Offline)](#1-live-cricket-scoring-online--offline)
2. [📊 Complete Match Scorecard & PDF Export](#2-complete-match-scorecard--pdf-export)
3. [📈 Match Analytics & Super Stars (MVP)](#3-match-analytics--super-stars-mvp)
4. [👤 Dynamic Player Profiles](#4-dynamic-player-profiles)
5. [🔥 Player Insights & Filtering](#5-player-insights--filtering)
6. [🆚 Player vs Player Comparison](#6-player-vs-player-comparison)
7. [🏏 Team Management](#7-team-management)
8. [📊 Team Statistics](#8-team-statistics)
9. [🆚 Team vs Team (H2H) Comparison](#9-team-vs-team-h2h-comparison)
10. [🏆 Tournament Management & Standings](#10-tournament-management--standings)
11. [📅 Match Schedule](#11-match-schedule)
12. [🏢 Club / Organization System](#12-club--organization-system)
13. [👥 Multiple Admins & Security Checks](#13-multiple-admins--security-checks)
14. [🏅 Hall of Fame](#14-hall-of-fame)
15. [🏆 Player Awards](#15-player-awards)
16. [📸 Sharing System](#16-sharing-system)
17. [🎙️ Automated Voice Commentary](#17-automated-voice-commentary)
18. [📡 Live Score Broadcasting](#18-live-score-broadcasting)
19. [⚙️ Match Editing & Post-Match Corrections](#19-match-editing--post-match-corrections)
20. [🔍 Global Search & Public Profiles](#20-global-search--public-profiles)
21. [📰 Cricket News](#21-cricket-news)
22. [🔢 Supported Cricket Formats & Ball Rules](#22-supported-cricket-formats--ball-rules)
23. [⚡ Live Tournament Draft Room](#23-live-tournament-draft-room)

---

## 1. Live Cricket Scoring (Online & Offline)

The scoring module provides an authoritative digital scoring interface. All scores are calculated from individual delivery events.

### A. Delivery Interface Specifications
*   **Runs off Bat**: Buttons to log `0` (dot), `1`, `2`, `3`, `4`, `6` runs.
*   **Extras logging**:
    *   *Wides*: Bowler is charged a wide; 1 extra run is debited to bowler and added to extras total. Delivery is marked illegal (`is_legal_delivery = false`).
    *   *No-Balls*: Bowler is charged a no-ball; 1 extra run is debited to bowler. Delivery is marked illegal. Supports optional custom penalties.
    *   *Byes*: Scored when batsmen run without ball touching bat/body. Added to team score as byes; not debited against bowler.
    *   *Leg-Byes*: Scored when ball touches batsman's pads/body. Added to team score as leg-byes; not debited against bowler.
*   **Wicket Recording Panel**: Triggers when a wicket falls. Requires selection of:
    *   *Dismissal Type*: Bowled, caught, caught & bowled, lbw, run out, stumped, hit wicket, retired hurt, retired out, obstructing the field, hit the ball twice, timed out, absent.
    *   *Fielder involvement*: Dropdown to select catching, run-out, or stumping fielder.
    *   *Dismissed Player*: Dropdown showing current batsmen on pitch.
    *   *Bowler Credit*: Automatically credits the bowler if dismissal is bowled, caught, lbw, stumped, or hit wicket. Explicitly denies credit for run-outs, obstructing the field, retired hurt, timed out, or hit the ball twice.

### B. Offline Scoring Engine (SQLite / Room Database)
*   **State Machine Cache**: When connection is lost, the local Room database caches the match state.
*   **Local Delivery Queue**: Saves deliveries locally with sequential UUID timestamps.
*   **Background Synchronizer**: Once connection is re-established, the queue attempts transmission. It checks `expected_revision` against server state to prevent duplicate submissions or write collisions.

### C. Match Setting Customizations
*   **Last Man Standing (LMS)**: When enabled, if 9 wickets fall, the remaining batsman continues batting alone. The non-striker remains on pitch as a runner only and cannot face balls.
*   **Overs Length**: Custom legal deliveries per over (default `6`, supports `7-ball`, `8-ball` or custom junior cricket specifications).
*   **Junior Cricket Custom Over Behavior**: Limits maximum runs or balls per over (e.g., maximum 8 deliveries per over even if further wides/no-balls are bowled, or capping runs at 10 per over).
*   **Wide/No-Ball Custom Penalties**:
    *   *Runs to Batsman*: Wide or no-ball runs are credited directly to the facing batsman's personal score instead of team extras.
    *   *Custom Extra Penalty*: Toggle to award `2` or `3` runs instead of `1` run for wides/no-balls.

---

## 2. Complete Match Scorecard & PDF Export

The scorecard represents a derived view reconstructed from the delivery database table.

### A. Sub-Component Layouts
*   **Match Summary Banner**: Styled header indicating match result (e.g., "Ali Panthers won by 5 wickets"), teams, venues, toss details, and player of the match.
*   **Batting Scorecard Card**:
    *   For each batsman: Batsman name, dismissal detail (e.g., "c Ahmed b Aliyan"), runs scored, balls faced, fours, sixes, and strike rate (runs/balls * 100).
    *   Indicates active batsmen with an asterisk (*).
*   **Bowling Scorecard Card**:
    *   For each bowler: Bowler name, overs bowled (legal balls / balls_per_over), maidens, runs conceded, wickets taken, wides conceded, no-balls conceded, and economy rate (runs conceded / overs bowled).
*   **Extras Breakdown Table**: Row displaying Wide (Wd), No-Ball (Nb), Bye (B), Leg-Bye (Lb), and Penalty runs, followed by total extras.
*   **Fall of Wickets Timeline**: Displays wickets chronologically (e.g., "1-24 (Ahmed, 3.2 overs), 2-48 (Aliyan, 6.4 overs)").
*   **Partnerships Board**: Tracks runs and balls shared between each batting pair. Displays partnership stats (e.g., "Partnership: 45 runs from 30 balls - Aliyan 28(18), Ahmed 14(12)").
*   **PDF Export Engine**: Converts the scorecard layouts into an offline A4 PDF report with custom tables, header logos, and dynamic summaries.

---

## 3. Match Analytics & Super Stars (MVP)

Provides visualizations and calculations indicating comparative match flow and individual MVP values.

### A. Analytical Charts
*   **Wagon Wheel Vector Chart**: Maps the angle of runs scored. Backend saves the angle coordinates `(x, y)` for each run-scoring delivery and UI draws vector lines from the pitch center to field boundaries (color-coded: singles are gray, 4s are blue, 6s are lime).
*   **Over Comparison Line Graph**: Compares the progressive run-rate of both teams over-by-over.
*   **Runs Breakdown Chart**: Horizontal bars displaying the percentage of runs scored in boundaries, singles, twos, threes, and extras.

### B. Super Stars (Real-Time MVP Points)
Calculates real-time player ratings using performance weights. The formula runs on every delivery:

$$\text{MVP Points} = \text{Batting Points} + \text{Bowling Points} + \text{Fielding Points}$$

*   **Batting Weightings**:
    *   `+1.0` point per run.
    *   `+1.0` bonus point per boundary four, `+2.0` bonus point per six.
    *   `+0.5` points per ball faced (rewards occupation).
    *   *Strike Rate Bonus*: If strike rate $> 150$ (minimum 10 balls faced), add `+10` points.
*   **Bowling Weightings**:
    *   `+20.0` points per wicket.
    *   `+10.0` points per maiden over.
    *   `+1.0` point per dot ball bowled.
    *   `-1.0` point per run conceded.
    *   `-2.0` points per wide/no-ball bowled.
*   **Fielding Weightings**:
    *   `+10.0` points per catch.
    *   `+15.0` points per stumping.
    *   `+15.0` points per run-out (split between thrower and keeper).
    *   `-5.0` points per dropped catch or misfield leading to boundary.

---

## 4. Dynamic Player Profiles

Comprehensive digital bio and career log for individual players.

### A. Profiles Tab Structure
*   **Overview Tab**: Player photo, basic bio, primary playing style (Right-hand batsman, Right-arm fast bowler, etc.), current club, and recent form indicators (last 5 innings scores).
*   **Career Statistics Card**:
    *   *Batting*: Matches, innings, runs, average (runs / times dismissed), strike rate, highest score, 50s, 100s.
    *   *Bowling*: Matches, overs, runs conceded, wickets, bowling average (runs conceded / wickets), economy rate, best bowling figures, five-wicket hauls.
    *   *Fielding*: Catches, stumpings, run-outs.
*   **Matches Log Tab**: Table of all matches played, showing date, teams, and the player's personal score and bowling metrics.
*   **Teams & Clubs Tab**: List of active and past club memberships.
*   **Tournaments Tab**: List of tournaments entered and titles won.
*   **Year-Wise Accordion**: Career metrics separated by year (e.g. 2026, 2025, 2024).

---

## 5. Player Insights & Filtering

Provides granular analytics going beyond basic career aggregates.

### A. Insight Charts
*   **Batting Analysis**: Form graphs showing moving average over the last 10 matches, strike-rate changes, and run distribution by bowler type (spin vs fast).
*   **Bowling Analysis**: Wickets log, run economy curves, and performance during powerplays vs death overs.
*   **Performance Against Teams**: Splits batting/bowling stats to show averages against specific rival teams.

### B. Query Filters
Users can filter all profile and insight statistics by:
*   **Match Format**: T20, ODI, Test, 100-Ball, T10, custom.
*   **Ball Type**: Leather ball vs. Tennis ball filters.
*   **Time Period**: Specific years or date ranges.
*   **Data Integrity**: Toggle to show only verified match scores or include manual/historical scores.

---

## 6. Player vs Player Comparison

Enables direct side-by-side comparison of any two players.

### A. Comparison Matrix Features
*   **Selector**: Search dropdowns to select Player A and Player B.
*   **Metric Grid**: Side-by-side comparative table:
    *   Matches played.
    *   Batting Runs, Average, Strike Rate, Highest Score, 50s/100s.
    *   Wickets, Bowling Average, Economy, Best Bowling.
    *   Catches & Stumpings.
*   **Visual Highlights**: Highlighting the higher value in green (e.g. if Aliyan's average is 31.5 and Ahmed's is 28.2, Aliyan's score cell is highlighted).

---

## 7. Team Management

Team profile dashboard and admin squad-building tools.

### A. Profiles & Assignments
*   **Team Overview**: Styled header displaying team name, short name, club logo, year founded, and overall win/loss record.
*   **Squad Panel**: Lists all players registered under the team, grouped by roles (Batters, Bowlers, All-rounders, Wicketkeepers).
*   **Role Officer Assignments**: Admin/Captain tool to assign official leadership tags:
    *   *Captain* (represented by a `(c)` tag).
    *   *Vice-Captain* (represented by a `(vc)` tag).
    *   *Wicketkeeper* (represented by a `(wk)` tag).
*   **Recent Form Cards**: Icons showing results of the last 5 matches (W, L, T, NR).

---

## 8. Team Statistics

Deep statistical analysis for teams.

### A. Metrics List (20+ Stats)
*   **General Performance**: Win percentage, loss percentage, tie percentage, no-result percentage.
*   **Toss Analysis**: Toss win ratio, choice to bat first ratio, win percentage when winning toss vs winning when losing toss.
*   **Batting/Bowling Index**: Average team score batting first, average team score chasing, highest total chased successfully, lowest total defended successfully.
*   **Phase Stats**: Average runs scored in powerplays, wickets lost in death overs, extras conceded per match.
*   **Individual Contributions**: List of top run-scorers and top wicket-takers for the team.

---

## 9. Team vs Team (H2H) Comparison

Head-to-head comparison tool for two teams.

### A. Head-to-Head Dashboard
*   **Historical Log**: Total head-to-head encounters, Team A wins, Team B wins, ties, no-results.
*   **Run Comparison**: Average score when playing against each other, highest score scored against rival.
*   **Previous Encounters Timeline**: Chronological card list of past matches played between the two teams, linking directly to historical scorecards.

---

## 10. Tournament Management & Standings

Tournament engine for creating, scheduling, and tracking competitions.

### A. League Governance
*   **Tournament Creation**: Configuration parameters (name, season, location, rule profiles, points allocation rules for win/loss/tie/no-result).
*   **Automatic Points Table**: Recalculates dynamically on match results:
    *   `Played (P)`: Incremented when a match is completed.
    *   `Won (W)`, `Lost (L)`, `Tied (T)`, `No-Result (NR)`: Awarded per tournament points configurations.
    *   `Points (Pts)`: Calculated as: 
        $$\text{Pts} = (\text{Wins} \times \text{Win Points}) + (\text{Ties} \times \text{Tie Points}) + (\text{NR} \times \text{NR Points})$$
*   **Net Run Rate (NRR) Formula**: Compiled at group stage conclusion:
    $$\text{NRR} = \left(\frac{\text{Total Runs Scored}}{\text{Total Overs Faced}}\right) - \left(\frac{\text{Total Runs Conceded}}{\text{Total Overs Bowled}}\right)$$
    *   *All-Out Adjustment*: If a team is bowled out before completing their full overs allocation, the calculation uses the full overs allocation (e.g. 20.0 overs) as the overs faced.

### B. Points-Table Possibility Calculator
*   Analyzes remaining tournament fixtures.
*   Computes potential final standing ranges for a team.
*   Displays scenarios needed for qualification (e.g. "Team A needs to win the next match with an NRR difference of +0.4 to qualify").

---

## 11. Match Schedule

Schedule coordinator for tournaments.

### A. Schedule System
*   **Fixture Creation**: Set scheduled date, time, venue, tournament group, and match teams.
*   **Status Indicators**: Display cards marked as `Scheduled`, `Live`, `Completed`, or `Postponed`.
*   **Share System**: Styled share buttons to generate fixture cards with team logos and dates.

---

## 12. Club / Organization System

Multi-tenant club hierarchy.

### A. Architectural Hierarchy
*   **Organization Node**: The root node representing a club or league organization.
*   **Seasons**: Sub-nodes containing tournaments and stats for a specific year (e.g. Season 2026).
*   **Tournaments & Teams**: Linked to the organization for centralized player eligibility tracking.
*   **Unified Player Stats**: Aggregate player data across all seasons run by the club.

---

## 13. Multiple Admins & Security Checks

System security and authorization policies.

### A. Admin Governance
*   **Multi-Admin Invitations**: Primary organization owner can invite other users as administrators, assigning permissions (e.g., manage tournaments, edit matches, manage squads).
*   **Audit Logging**: The backend [AuditLog.php](file:///c:/Users/Muhammad%20Aliyan/Downloads/cricket-draft-source/app/Models/AuditLog.php) model tracks all admin changes, recording the IP address, user agent, change data, and timestamps.
*   **Super Admin Governance**: Super Admins retain global system visibility, API client toggles, and session revocations.

---

## 14. Hall of Fame

Seasonal honors board at club/organization level.

### A. Criteria & Honors
*   **Performance Benchmarks**: Automatically flags players achieving milestone metrics within a season (e.g. $>500$ runs, or $>25$ wickets).
*   **Leaderboards**: Visual lists showing overall top performers across years.

---

## 15. Player Awards

Profile trophies and tournament awards.

### A. Awards System
*   **Trophy Allocations**: Admins can assign verified accolades to players upon tournament or match completion:
    *   *Player of the Match (POTM)*
    *   *Player of the Tournament (POTT)*
    *   *Best Batsman*
    *   *Best Bowler*
*   **Badge Display**: Displays these awards as graphic badges at the top of player profiles.

---

## 16. Sharing System

Media share card generation.

### A. Graphical Share Cards
*   Uses HTML5 Canvas (Web) or Android native drawing to compile structured data into visually appealing PNG images.
*   Includes match results, player milestone cards (e.g., "Aliyan scored 100 runs!"), points tables, or team squads.
*   Allows direct share actions to messaging platforms.

---

## 17. Automated Voice Commentary

Automated text-to-speech commentary.

### A. TTS Event Triggers
*   **Run Events**: TTS engine speaks delivery results (e.g. "Four runs! Nicely played by striker").
*   **Wickets**: Announces dismissals (e.g. "Wicket! Aliyan is caught by Ahmed. Bowled by Khan").
*   **Milestones**: Detects and announces personal achievements (e.g. "Fifty runs up for Aliyan!").

---

## 18. Live Score Broadcasting

Real-time delivery broadcasting to public scoreboards.

### A. Broadcast Flow
*   **Scorer**: Log delivery on the mobile scoring interface.
*   **Transmission**: Scorer submits delivery payload containing revised match metrics.
*   **Server Broadcast**: Server stores delivery and broadcasts update via WebSocket channels (or polls every 2 seconds).
*   **Public Board**: Refresh scorecard UI elements automatically for spectators, updating totals, current batsman, and over notations.

---

## 19. Match Editing & Post-Match Corrections

Allows editing scores and cards after matches are saved.

### A. Corrective Mechanisms
*   **Score Correction**: Admins can edit individual delivery records in the match database.
*   **Player Swapping**: Corrects accidental data entry errors (e.g., swapping runs logged under Striker A to Striker B).
*   **Recalculation Triggers**: On delivery correction, the system triggers rebuild routines to recalculate batting stats, bowling stats, partnerships, extras, and overs faced.
*   *Partnership Edge Case*: Recalculation rebuilds the partnership sequence chronologically from delivery zero to ensure partnership boundaries align correctly.

---

## 20. Global Search & Public Profiles

Central search index.

### A. Search Scopes
*   **Unified Search**: Search players, teams, tournaments, clubs, and matches from a single query box.
*   **Public Profiles Control**: Player option to toggle profile visibility status (`public` or `private`). Private profiles are redacted in public searches.

---

## 21. Cricket News

In-app news feed.

### A. News System
*   Integrates RSS feeds or admin posts to display tournament announcements, rules changes, and league articles.

---

## 22. Supported Cricket Formats & Ball Rules

Flexible format support.

### A. Format Profiles
*   **Formats**: T20, ODI, Test matches, 100-Ball format, T10, custom.
*   **Overs and Innings Limits**: Handled via custom profiles (Test supports 2 innings per side with unlimited overs; 100-Ball supports 100 deliveries per side with 5-ball or 10-ball over shifts).
*   **Ball Type Configurations**: Tennis vs. Leather ball filtering.

---

## 23. Live Tournament Draft Room

Admins can enable a draft when setting up a tournament.

### A. Draft Setup
*   Admins toggle Draft Mode during tournament configuration.
*   Setup round counts, pick allocations, pick order (fixed or snake draft), and countdown timer durations.

### B. Live Clock & Captain Interface
*   Captains enter the virtual draft room.
*   The team on the clock has `pick_duration` seconds (e.g., 60s) to select a player from the approved player pool.
*   Displays a live countdown timer. If the timer reaches `0`, the pick is marked `expired`.

### C. Admin Overseer Control
*   Admins can pause/resume draft rounds, extend pick timers, skip expired turns, reassign selections, or undo previous picks.

### D. Sync & Transaction Integrity
*   Database updates are protected by pessimistic locking.
*   Real-time polling or WebSockets broadcast changes to sync selection pools instantly across all captain screens.
