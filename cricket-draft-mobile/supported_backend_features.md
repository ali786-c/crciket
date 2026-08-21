# Supported Backend Features Specification

This document lists and explains all cricket management and scoring features already implemented and fully supported by the Laravel backend. These components are ready to be integrated into the mobile app.

---

## 📋 Features Index

1. [🔒 Authentication & Security (Spatie RBAC / Sanctum)](#1-authentication--security-spatie-rbac--sanctum)
2. [🏆 Tournament Administration & Configurations](#2-tournament-administration--configurations)
3. [🏏 Team & Squad Leadership Management](#3-team--squad-leadership-management)
4. [👤 Player Profile Onboarding & Approvals](#4-player-profile-onboarding--approvals)
5. [⚡ Transactional Live Draft Room (Comprehensive)](#5-transactional-live-draft-room-comprehensive)
6. [📅 Fixtures & Schedule Coordinator](#6-fixtures--schedule-coordinator)
7. [⚔️ Match Setup & Toss Logic](#7-match-setup--toss-logic)
8. [🏏 Live Scoring Ledger (Derived Scorecard)](#8-live-scoring-ledger-derived-scorecard)
9. [🏆 Match Results & Standings Table (with NRR)](#9-match-results--standings-table-with-nrr)
10. [⚙️ System Auditing & Super Admin Governance](#10-system-auditing--super-admin-governance)
11. [🏏 Customizable Scoring & Over Structures](#11-customizable-scoring--over-structures)
12. [📶 Offline Batch Scoring Sync API](#12-offline-batch-scoring-sync-api)
13. [📈 Match Analytics, Wagon Wheel & MVP Engine](#13-match-analytics-wagon-wheel--mvp-engine)
14. [🆚 Side-by-Side Comparisons (Player & Team H2H)](#14-side-by-side-comparisons-player--team-h2h)
15. [👮 Team Officer Assignments & Squad Groupings](#15-team-officer-assignments--squad-groupings)
16. [🏆 Standings Simulation (Possibilities Calculator)](#16-standings-simulation-possibilities-calculator)
17. [🏢 Multi-Tenant Clubs & Seasons](#17-multi-tenant-clubs--seasons)
18. [📢 Automated TTS Commentary Strings](#18-automated-tts-commentary-strings)
19. [⚙️ Post-Match Corrections (Scorecard Recalculations)](#19-post-match-corrections-scorecard-recalculations)
20. [🔍 Unified Search & News Feed APIs](#20-unified-search--news-feed-apis)

---

## 1. Authentication & Security (Spatie RBAC / Sanctum)
The backend provides a secure authentication wrapper for all web and mobile operations.
*   **API Authentication**: Managed via Laravel Sanctum tokens. Endpoints support login, token issuance, active user details retrieval (`/auth/me`), single-device logout, and all-devices session revocation.
*   **Role-Based Access Control (RBAC)**: Managed via Spatie roles and permissions. Pre-defined roles:
    *   `super_admin`: Global system configurations and token registry actions.
    *   `admin`: Tournament schedules, team approvals, and control actions.
    *   `captain`: Draft picks and Playing XI submissions.
    *   `player`: Onboarding profiles and registration requests.

---

## 2. Tournament Administration & Configurations
Governance endpoints to set up, edit, and query active cricket tournaments.
*   **Configurations**: Name, season, location, venue coordinates, timezone mapping, starts/ends date boundaries, registration windows, and default overs per innings limits.
*   **API Routes**: Supports list queries, detail views, and state transition updates.

---

## 3. Team & Squad Leadership Management
Centralized squad and team coordination files.
*   **Management Actions**: Add teams to tournaments, view active team squads, and export captains lists in CSV format.
*   **Officer Actions**: Admins can assign or revoke the `captain` role for a specific team, establishing the captain user assignment as the selector for draft turns.

---

## 4. Player Profile Onboarding & Approvals
System rules governing player profiles and league entries.
*   **Player Profiles**: Captures name, contact numbers, primary playing role (Batter, Bowler, All-rounder, Wicketkeeper), styles (e.g. Right-hand bats), city, and bio details.
*   **Approval Queue**: Players submit tournament registration requests. Admins view, approve, or reject registrations, building the active draft-eligible pool.
*   **Import**: Supports CSV imports of player registration arrays.

---

## 5. Transactional Live Draft Room (Comprehensive)
A highly sophisticated draft center with full state locks and safety rules.
*   **Rounds Generator**: Automatically generates draft rounds, team sequence numbers, and pick schedules (configured via form payloads or CSV files).
*   **Pessimistic State Locks**: Draft turns are transactionally protected. Captain selections run inside database locks to prevent concurrent selection conflicts.
*   **Draft Live Clock**: Monitors countdown duration for active turns. If the duration expires, the turn moves to `expired`.
*   **Admin Overseer Operations**:
    *   *Pause & Resume*: Halts the countdown clock.
    *   *Extend Timer*: Resets the countdown clock with additional seconds.
    *   *Skip Pick*: Forces an expired turn to transition, moving the clock to the next team in line.
    *   *Player Override*: Enables admins to manually pick a player on behalf of a team.
    *   *Undo Pick*: Voids the latest selection, rolling the active turn back to the previous team.
    *   *Reassign Pick*: Swaps player assignments between selected and pending slots.
*   **Revision Synchronization**: Broadcasts draft mutations using an incremental `revision` counter to sync screens instantly.

---

## 6. Fixtures & Schedule Coordinator
The bridge between planning tournaments and active match scoring.
*   **Scheduling**: Create and edit match cards (venue location, round names, match numbers, scheduled times in specific timezones).
*   **Handoff Engine**: Fixtures validate scheduling conflicts (checking if teams are already scheduled to play at the target time). When starting a match, the fixture transitions status to `in_progress` and hands control over to an operational Match record.

---

## 7. Match Setup & Toss Logic
Preparation sequence before live scoring commences.
*   **Match Squads**: Creates match rosters snapshotting player profiles.
*   **Lineup Submission**: Captains propose their Playing XI, which admins approve.
*   **Toss Recording**: Logs the toss winner and the tactical decision to bat or bowl first.

---

## 8. Live Scoring Ledger (Derived Scorecard)
Scoring data is calculated programmatically from individual delivery events to guarantee MCC compliance.
*   **Delivery Ledger**: Logs balls chronologically with striker, non-striker, bowler, runs scored off the bat, extras (wides, no-balls, byes, leg-byes), wicket IDs, and commentary text.
*   **Automated Stats Rebuilds**: The scoring engine listens to delivery inputs and automatically updates personal statistics tables:
    *   *InningsBattingStat*: Tracks batsman position, total runs, balls faced, boundaries (4s, 6s), strike rates, and dismissal methods.
    *   *InningsBowlingStat*: Tracks overs bowled, maidens, runs conceded, wickets, wides, no-balls, and economy rates.
*   **Match Undo**: Scorers can undo the latest delivery, which deletes the delivery record, restores previous batsman states, and recalculates the innings cache.
*   **Innings Transition**: Handles innings completions (reaches over limits, all-out, or chases down target) and sets targets for the chasing team.

---

## 9. Match Results & Standings Table (with NRR)
End-of-match verification pipeline.
*   **Submission**: Scorers submit match results for admin review.
*   **Approval**: Once approved, the result is frozen and stand-alone statistics are written to tournament standing tables.
*   **NRR Calculation**: Rebuilds Net Run Rates automatically:
    $$\text{NRR} = \left(\frac{\text{Runs Scored}}{\text{Overs Faced}}\right) - \left(\frac{\text{Runs Conceded}}{\text{Overs Bowled}}\right)$$

---

## 10. System Auditing & Super Admin Governance
Platform control panels.
*   **Auditing Logs**: Audits every administrative change (e.g. draft overrides, result updates, player registration edits), storing before/after details, user IDs, and IPs.
*   **Governance Views**: Exposes dashboard endpoints for Super Admins to monitor DB status, queue size, route registries, and toggle/revoke API client credentials.

---

## 11. Customizable Scoring & Over Structures
Customized matches and rule templates.
*   **Dynamic Over Size**: Restricts balls count per over (e.g. 7-ball or 8-ball overs) using rule profile profiles.
*   **LMS Batting**: Allows a single batter to bat alone after 9 wickets fall under Last Man Standing rules.
*   **Batsman run credits**: Automatically credits runs scored on wides/no-balls to the batsman when enabled.
*   **Junior Cricket limits**: Limits runs or balls allowed in a single over.

---

## 12. Offline Batch Scoring Sync API
Deduplicated batch offline synchronization.
*   **Sync Endpoint**: Exposes `/api/v1/matches/{match}/deliveries/sync` to upload chronological sequences of deliveries scored offline.
*   **Deduplication**: Filters duplicate requests using `local_uuid` and `device_timestamp` mapping parameters.

---

## 13. Match Analytics, Wagon Wheel & MVP Engine
Visual vector mapping and real-time performance leadership leaderboards.
*   **Wagon Wheel Mapping**: Stores and validates spatial vector coordinates `wagon_x` and `wagon_y` (decimal percentage bounds: `0.00` to `100.00`) on delivery registers.
*   **MVP point system**: Dynamically weights runs, milestones, wickets, catches, stumpings, run-outs, and extras penalties to render live MVP leaderboards.

---

## 14. Side-by-Side Comparisons (Player & Team H2H)
Side-by-side comparative indexes.
*   **Player Comparisons**: Returns comparative aggregates (batting averages, strike rates, bowling economies) side-by-side.
*   **Team H2H**: Fetches wins, losses, ties, and past match encounter histories for two selected teams.

---

## 15. Team Officer Assignments & Squad Groupings
Leadership assignments and role-based classifications.
*   **Designation flags**: Sets Captain, Vice-Captain, and Wicketkeeper designations inside database draft picks.
*   **Squad grouping**: Returns rosters grouped by Batter, Bowler, Wicketkeeper, and All-rounder categories.

---

## 16. Standings Simulation (Possibilities Calculator)
Win/loss simulation analyzer.
*   **Qualifications Simulator**: Scans scheduled fixtures and points standings to return qualification forecasts (`qualified`, `eliminated`, `in_contention` margins).

---

## 17. Multi-Tenant Clubs & Seasons
Parent-child tenant containers.
*   **Multi-tenant support**: Adds `organizations` and `seasons` hierarchy layers wrapping independent tournaments.

---

## 18. Automated TTS Commentary Strings
Speech-friendly voice feeds.
*   **Commentary Generator**: Automatically generates conversational speech-friendly sentences for live scorecard updates.

---

## 19. Post-Match Corrections (Scorecard Recalculations)
Post-scoring corrective entries.
*   **Recalculations**: Modifies historic match deliveries and sequentially rebuilds batsman/bowler figures and match standings chronologically.

---

## 20. Unified Search & News Feed APIs
*   **Global Search Index**: Searches across players, teams, matches, and tournaments under a single endpoint.
*   **News Feed**: Serves public published cricket news articles and slug registries.

