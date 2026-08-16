# Cricket Draft OS: Seasons, Divisions, Fixtures, Standings, History, and Archive Expansion Plan

## Executive direction

The current system already has a strong draft engine, tournament configuration, captain workflow, public live board, reports, PDF exports, role authorization, audit logs, and server-authoritative timers. The next expansion should transform it from a **draft-only platform** into a complete cricket competition operations system.

The recommended architecture is deliberately incremental. Existing live draft functionality must continue working while new season, division, fixture, result, standings, team-history, and archive modules are added around it. The draft remains one phase of a larger competition lifecycle rather than being replaced.

> **Core hierarchy:** Season → Division → Tournament/Competition Edition → Draft, Fixtures, Results, Standings, Reports, Archive.

## 1. Product vocabulary and boundaries

A clear vocabulary is necessary before database work begins. The terms below should be used consistently in URLs, permissions, database tables, reports, and UI labels.

| Term | Meaning | Example |
|---|---|---|
| Season | A time-based competition container | 2026 Season |
| Division | A competition group inside a season | Premier, Gold, Silver, Regional North |
| Tournament | One independently operated competition edition | Preview Cricket Cup |
| Tournament template | Reusable blueprint for teams, rounds, pick assignments, and default rules | Two-team six-pick draft template |
| Club/team identity | The long-term identity that can participate across seasons | Ali Panthers |
| Tournament team entry | A club’s participation in one tournament/division | Ali Panthers in 2026 Premier |
| Fixture | A scheduled match between two tournament team entries | Ali Panthers vs Lahore Lions |
| Match result | Official score/result submitted and approved for a fixture | Ali Panthers won by 6 wickets |
| Standings | Calculated points-table state for a division or tournament | Played, won, lost, points, NRR |
| Archive | Read-only historical view after a season or tournament closes | 2026 Premier Archive |

The existing `tournaments` table should remain the operational competition record. New season and division records should be attached to it instead of renaming or breaking the current draft tables.

## 2. Target lifecycle model

### Season lifecycle

A season begins in `planned` status. Admins can configure divisions, participating tournaments, venues, and dates. Once registration opens, the season moves to `registration`. During active fixtures it becomes `live`. After all competitions and result approvals are complete, it moves to `completed`. Once reports and records are frozen, it becomes `archived`.

| Season status | Allowed next states | Meaning |
|---|---|---|
| planned | registration, cancelled | Configuration is being prepared |
| registration | live, cancelled | Teams and tournaments may be registered |
| live | completed, suspended | Fixtures and competition operations are active |
| suspended | live, cancelled | Operations are temporarily frozen |
| completed | archived | Results are final and season is ready for history |
| archived | none, or controlled restore by super admin | Read-only historical state |
| cancelled | none | Season was stopped without completion |

### Tournament lifecycle

The current tournament lifecycle should be extended without weakening its existing safety rules.

| Tournament status | Allowed next states | Main controls |
|---|---|---|
| draft | registration, cancelled | Configure metadata, teams, draft, fixtures |
| registration | ready, cancelled | Accept player/team registrations |
| ready | live, cancelled | Verify draft and fixture setup |
| live | completed, suspended | Operate draft, matches, results |
| suspended | live, completed, cancelled | Freeze operational changes |
| completed | archived | Finalize reports and history |
| archived | none, or controlled restore | Read-only public history |
| cancelled | none | No operational actions |

Every transition must be executed through a server-side service, validated against the current state, recorded in the audit log, and rejected if the transition is invalid.

### Fixture lifecycle

Fixtures require their own lifecycle because they can be postponed or rescheduled without changing the entire tournament status.

| Fixture status | Meaning |
|---|---|
| scheduled | Date, time, venue, and teams are confirmed |
| live | Match is currently being played |
| result_pending | Score has been submitted and awaits approval |
| completed | Result approved and included in standings |
| postponed | Match moved to a future schedule |
| abandoned | Match stopped without a valid result |
| cancelled | Match removed from the competition |

### Result lifecycle

A scorer or authorized match operator should submit a result. A separate result approver should approve it. Captains must not be able to publish final results directly.

| Result status | Meaning |
|---|---|
| draft | Score entry is incomplete |
| submitted | Scorer submitted the result |
| approved | Result is official and affects standings |
| rejected | Approver requested correction |
| superseded | Replaced by a later approved correction |

## 3. Permission model

The existing Spatie permission system should be extended with operational permissions rather than giving every admin unrestricted access.

| Role | Main capabilities |
|---|---|
| Super admin | All permissions, archive restore, permission management, system settings |
| Season manager | Seasons, divisions, templates, tournament lifecycle, archives |
| Tournament manager | Tournament configuration, teams, player approvals, draft control |
| Scheduler | Venues, fixtures, rescheduling, fixture publication |
| Scorer | Enter match scores and submit results |
| Result approver | Approve/reject results and finalize standings updates |
| Report viewer | View and download authorized reports only |
| Captain | Own team draft selections, own squad, team history, authorized reports |
| Player | Own profile, registration, personal draft history |
| Public viewer | Published season, fixture, result, standings, squad, and archive pages |

Destructive actions should require a confirmation reason and be recorded with actor, timestamp, IP, user-agent, before-state, and after-state.

## 4. Domain model and database design

### 4.1 Seasons

Create a `seasons` table with:

- `id`.
- `name`, `slug`, and optional `short_name`.
- `year`.
- `description`.
- `starts_on`, `ends_on`, and `timezone`.
- `status`.
- `is_public`.
- `logo_path`, `banner_path`.
- `published_at`, `completed_at`, `archived_at`.
- `created_by` and timestamps.

Indexes should cover `slug`, `year`, `status`, and public discovery queries. The slug must be unique.

Add `season_id` to `tournaments`. A tournament belongs to one season. During transition, `season_id` may be nullable for old tournaments, but all newly created tournaments should require a season.

### 4.2 Divisions

Create a `divisions` table:

- `id`.
- `season_id`.
- `name`, `slug`, and `code`.
- `description`.
- `display_order`.
- `format` such as round-robin, league, knockout, or custom.
- `status`.
- `points_rule_id` or a division-level rule configuration.
- `is_public`.
- Timestamps.

A division belongs to a season. A tournament may belong to one division, while a season may contain many divisions and many tournaments.

### 4.3 Long-term team identity

The current `teams` table is tied to a tournament, which is appropriate for the current draft system but insufficient for historical team tracking. Add a global `clubs` or `team_profiles` table:

- `id`.
- `name`, `short_name`, `slug`.
- `logo_path`, `primary_color`, `secondary_color`.
- `city`, `home_venue`.
- `founded_year`.
- `is_active`.

Add a nullable `club_id` to the existing `teams` table. The existing team record remains the tournament-specific entry, while `club_id` connects the entry to the long-term identity. This avoids a risky immediate table rename.

Future team history will be based on `club_id` plus tournament/season entries, not only on the current tournament-specific team ID.

### 4.4 Tournament templates

Create:

- `tournament_templates`.
- `template_rounds`.
- `template_picks`.
- Optional `template_teams`.

A template stores configuration, not live data. It may contain default squad size, default pick duration, round names, pick numbers, assigned team slots, draft rules, and fixture format. It must never contain selected players, audit logs, captain assignments, match results, or historical standings.

### 4.5 Venues

Create a reusable `venues` table:

- `id`.
- `name`.
- `short_name`.
- `city`, `country`.
- `address`.
- `timezone`.
- `capacity`.
- `surface_type`.
- `logo/photo` optional.
- `is_active`.

A fixture stores `venue_id`, but should also retain a snapshot of venue name if historical accuracy is important.

### 4.6 Fixtures

Create a `fixtures` table:

- `id`, `season_id`, `division_id`, `tournament_id`.
- `round_label`, `match_number`, `week_number`.
- `home_team_id`, `away_team_id`.
- `venue_id`.
- `scheduled_at`, `timezone`.
- `status`.
- `published_at`, `started_at`, `completed_at`.
- `notes`.
- `created_by`, `updated_by`.

Add unique protection so the same match number cannot be duplicated inside one tournament. Validate that both teams belong to the same tournament/division and that a team cannot play two fixtures at the same time.

### 4.7 Match results and scorecards

Create a `match_results` table:

- `id`, `fixture_id`, `version`.
- `status`.
- `submitted_by`, `submitted_at`.
- `approved_by`, `approved_at`.
- `winner_team_id`.
- `result_type`: win, tie, no_result, abandoned, draw.
- `win_margin_type`: runs, wickets, innings, or none.
- `win_margin_value`.
- `home_runs`, `home_wickets`, `home_overs`.
- `away_runs`, `away_wickets`, `away_overs`.
- `player_of_match_id` optional.
- `notes`.
- `rejection_reason`.

Create a separate `match_result_events` or `result_revisions` table if corrections must preserve every previous score version. The approved result should be immutable except through a correction workflow that creates a new version.

### 4.8 Points rules and standings

Create:

- `points_rules`.
- `standings`.
- Optional `standings_snapshots`.

A default rule configuration could be:

| Outcome | Points |
|---|---:|
| Win | 2 |
| Tie | 1 |
| No result | 1 |
| Loss | 0 |
| Abandoned | Configurable |

A standings row should contain:

- Played, won, lost, tied, no-result, abandoned.
- Points.
- Runs scored and conceded.
- Overs faced and bowled.
- Net run rate.
- Position.
- Form sequence.
- Last calculated timestamp.

The calculation must happen in a dedicated `StandingsService` after an approved result. It must be transactional and idempotent: recalculating the same approved result must not double-count points. A correction should rebuild standings from all approved results or from a verified snapshot plus the remaining results.

Net run rate should only be calculated when sufficient innings/overs data exists. If a competition chooses not to use NRR, position tie-break rules should be configurable, such as points, wins, NRR, head-to-head, then team name.

## 5. Tournament templates and duplication workflow

The template feature should save admin time without accidentally copying live state.

### Template creation

Admin selects an existing tournament and chooses **Save as template**, or creates a blank template. The system copies:

- Tournament-level draft defaults.
- Round names and order.
- Pick numbers and team-slot assignments.
- Pick durations.
- Squad rules.
- Fixture format and points-rule defaults.

The system must not copy:

- Selected players.
- Captain assignments.
- Registrations.
- Audit logs.
- Timer state.
- Draft revisions.
- Fixtures with results.
- Standings.

### Create from template

The admin chooses a target season and division, enters a new tournament name/slug, chooses dates and visibility, maps template team slots to actual clubs, and confirms the duplication. The new tournament starts in `draft` or `registration` status with a new ID and clean state.

The duplication must run inside a database transaction. If any team mapping or pick assignment fails, the entire creation must roll back. The result should show a duplication summary and link to the new tournament.

## 6. Fixture scheduling design

### Fixture generation modes

Support three initial modes:

1. **Manual scheduling:** admin creates each fixture and selects date, time, venue, and teams.
2. **Round-robin generation:** system generates single round-robin pairings for all participating teams.
3. **Template schedule:** a tournament template contains a schedule pattern that is applied to the selected teams.

Knockout brackets should be postponed until the league/round-robin model is stable because they require bye handling, qualification rules, and bracket progression.

### Scheduling rules

The server should validate:

- Both teams belong to the tournament.
- Teams are active and eligible.
- Home and away teams are different unless an exhibition match is explicitly allowed.
- No team has overlapping fixtures.
- Venue is active and available at the requested time.
- Fixture date lies within the tournament/season window.
- Timezone is explicit and converted to UTC for storage.
- Published fixture edits require a reschedule permission and audit reason.

### Fixture UI

Admin needs a calendar/list toggle, filters by season/division/status/team/venue, create/edit fixture form, bulk generation wizard, reschedule workflow, and publish/unpublish controls. Public users need fixtures grouped by date and division. Captains need their own team’s upcoming and completed fixtures.

## 7. Match scoring and approval workflow

A scorer opens a fixture and enters the basic scorecard. The system should initially support match-level scoring, then expand into ball-by-ball scoring later.

### Initial match-level score entry

The first release should capture:

- Both team scores.
- Wickets.
- Overs.
- Winner/result type.
- Margin.
- Player of the match.
- Notes and attachments if required.

The scorer submits the result. The result approver reviews the data, sees the standings impact preview, and either approves or rejects it. Only approved results update the public result page and points table.

### Approval safeguards

Before approval, validate:

- Winner is one of the fixture teams or result type is tie/no-result/abandoned.
- Score values are non-negative.
- Overs are valid.
- Winner and margin are consistent.
- Fixture is not already cancelled.
- Duplicate approved result does not exist.
- The approver is not forced to approve an incomplete score.

A correction after approval must not overwrite history silently. It should create a new result revision, reverse the previous standings contribution, apply the new contribution, and record the reason.

## 8. Team history

The team history page should combine long-term club identity with season-specific participation.

Each club page should show:

- Club logo, name, city, venue, and colors.
- Seasons participated.
- Division and tournament entries.
- Captains by season.
- Drafted squad by season.
- Retained/released players.
- Fixtures and results.
- Win/loss record.
- Points-table finishes.
- Awards and player-of-match records.

A season snapshot must preserve the squad as it existed at the time. If a player is later transferred or removed, historical reports must continue showing the original approved squad and the date of the change.

## 9. Archive design

An archived competition should be operationally read-only. Public users should be able to browse past seasons without seeing draft controls or unpublished personal data.

### Archive navigation

Recommended public routes:

- `/seasons` — all published seasons.
- `/seasons/{season}` — season overview.
- `/seasons/{season}/divisions/{division}` — division overview.
- `/tournaments/{tournament}` — tournament summary.
- `/tournaments/{tournament}/fixtures` — fixtures and results.
- `/tournaments/{tournament}/standings` — final/current table.
- `/teams/{club}` — team history.
- `/archive` — completed seasons and tournaments.

### Archive freeze rules

When a season becomes archived:

- Draft configuration is locked.
- Player registrations are locked.
- Fixtures and approved results are locked.
- Standings are frozen as a snapshot.
- Reports remain downloadable.
- Public pages remain available according to visibility.
- Only super admin can request a controlled restore, with a mandatory reason and audit entry.

## 10. Public and authenticated experiences

### Admin workspace

Add a top-level competition management area with season selector, division selector, tournament portfolio, fixture calendar, result approval queue, standings preview, template library, archive controls, and report center.

### Captain workspace

Add cards for current team squad, upcoming fixtures, completed results, current standings position, season history, and team PDF reports. Captains should see only their team’s private operational information.

### Player workspace

Add registered seasons, drafted teams, fixture participation, personal statistics when published, and player history. Personal contact details must never appear in public archive pages.

### Public experience

Add season landing pages, division tabs, fixture/result pages, live score indicators, standings, team profiles, player profiles, archive browsing, and safe downloadable reports. Public pages should never expose private emails, internal audit data, IP addresses, pending registrations, or unpublished result drafts.

## 11. Reporting expansion

The existing report system can be extended with audience-safe reports.

| Report | Admin | Captain | Public |
|---|---|---|---|
| Season summary | Full | Published summary | Published summary |
| Division standings | Full | Published standings | Published standings |
| Fixture list | Full | Own team and public fixtures | Published fixtures |
| Result report | All revisions | Approved results involving team | Approved public results |
| Team history | All teams | Own team | Published team history |
| Draft history | Full operational history | Own team scope | Public selections only |
| Audit report | Authorized admin only | No | No |
| Registration report | Full | No | No |

All PDF downloads should use tournament/season branding and must not display audience labels such as “Admin report” or “Captain report.”

## 12. Implementation phases

### Phase 0 — Architecture and migration safety

Document the final domain model, add feature flags, create migrations without changing current live behavior, and add seed data for one season, two divisions, and one archived tournament. This phase must finish before UI expansion.

### Phase 1 — Seasons and divisions

Implement season CRUD, division CRUD, season lifecycle, public visibility, admin permissions, season navigation, and attach existing tournaments to seasons/divisions. Add migration/backfill support for old tournaments.

**Exit gate:** Existing draft tests remain green, old tournament URLs still work, and a tournament can be located through its season/division.

### Phase 2 — Clubs and team history foundation

Create global club identity, connect existing tournament teams to clubs, add club CRUD, logo/colors, season participation, and historical squad snapshots.

**Exit gate:** One club can participate in two seasons while each season retains independent squad and captain history.

### Phase 3 — Tournament templates

Implement save-as-template, template library, create-from-template wizard, team mapping, clean-state cloning, transactional rollback, and audit logs.

**Exit gate:** Cloning a tournament creates a new independent draft with no copied picks, timers, registrations, audit logs, fixtures, or standings.

### Phase 4 — Venues and fixtures

Implement venue management, manual fixture creation, round-robin generation, calendar/list UI, timezone conversion, availability validation, rescheduling, publishing, and public fixture pages.

**Exit gate:** No team overlap, no venue overlap, UTC storage is correct, and all reschedules are audited.

### Phase 5 — Results and standings

Implement scorer role, result entry, approval queue, result revisioning, points rules, standings calculation, tie-break rules, NRR support, and public table pages.

**Exit gate:** Approved results update standings exactly once; rejected/draft results do not affect public tables; corrections preserve history.

### Phase 6 — Team history and archive

Implement historical team pages, season summaries, archive freeze, read-only middleware, snapshots, archive reports, search, and public navigation.

**Exit gate:** Archived content remains viewable and downloadable, while mutation endpoints reject non-authorized changes.

### Phase 7 — Advanced UX and operations

Add calendar improvements, live score indicators, notifications, dashboard analytics, bulk imports, exports, backups, monitoring, and deployment documentation.

### Phase 8 — Optional advanced cricket modules

Only after the league lifecycle is stable should the system add ball-by-ball scoring, player statistics, trades, transfers, replacement players, knockout brackets, auction mode, or WebSocket scaling.

## 13. Testing strategy

Every module requires unit, feature, authorization, and browser smoke tests.

### Required test groups

- Season lifecycle transitions.
- Division ownership and visibility.
- Tournament-to-season compatibility.
- Template clone isolation.
- Template rollback on invalid mapping.
- Club participation across multiple seasons.
- Fixture team and venue overlap prevention.
- Timezone conversion and date-window validation.
- Scorer submission authorization.
- Result approver authorization.
- Invalid score and winner rejection.
- Approved result idempotency.
- Result correction and standings rebuild.
- Points and tie-break calculations.
- Archive mutation rejection.
- Public privacy redaction.
- Captain team-scope enforcement.
- PDF content and audience boundaries.
- Audit coverage for every lifecycle mutation.
- Concurrency tests for result approval and fixture edits.

### Acceptance targets

The existing draft regression suite must remain fully green throughout the expansion. Every new mutation endpoint should have unauthorized, invalid-state, duplicate-request, and valid-flow tests. Production deployment should include migration rollback checks, cache/config rebuild checks, scheduled task verification, and a backup restore rehearsal.

## 14. Recommended first release scope

The full vision is large, so the first release should contain:

1. Seasons.
2. Divisions.
3. Tournament-to-season/division linking.
4. Global clubs and tournament team entries.
5. Tournament templates.
6. Venues.
7. Manual fixtures.
8. Basic match result submission and approval.
9. Points table with win/tie/loss rules.
10. Team history snapshots.
11. Completed tournament archive.
12. Public season/division/standings pages.
13. Admin, captain, and public PDF reports.

Round-robin generation, NRR, result revisions, bulk import, player statistics, and knockout brackets should follow after this foundation has been verified.

## 15. Key design decisions to confirm before coding

The following decisions should be approved before database migrations begin:

| Decision | Recommended default |
|---|---|
| Season-to-tournament relationship | One season has many tournaments |
| Tournament-to-division relationship | One tournament belongs to one division; division may be nullable for legacy records |
| Long-term team model | Add global clubs and connect existing tournament teams with `club_id` |
| Initial competition format | Manual fixtures first, round-robin generator second |
| Initial scoring depth | Match-level scorecard first, ball-by-ball later |
| Default win points | 2 |
| Default tie/no-result points | 1 |
| Standings calculation | Rebuildable, transactional, idempotent service |
| Archive behavior | Public read-only, super-admin controlled restore |
| Historical squad data | Immutable season/tournament snapshots |
| Template cloning | Deep copy configuration only, never live state |
| Time storage | UTC in database, selected timezone in UI |
| Public privacy | Approved and published data only |

## Final recommendation

The safest implementation order is **Seasons and divisions → clubs and team history foundation → templates → venues and fixtures → results and standings → archive**. This order creates the correct data relationships before reports and public pages depend on them.

The existing draft system should remain the source of truth for player selection. The new competition layer should reference draft outcomes rather than duplicating them. Once the first release is stable, the platform will support a complete journey: configure a season, create divisions, create a tournament from a template, draft squads, schedule fixtures, approve results, calculate standings, preserve team history, and publish a read-only archive.
