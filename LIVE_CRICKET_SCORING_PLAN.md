# Live Cricket Match Scoring and Scorecard Expansion Plan

## Executive decision

Yes, this feature should be part of the system. The draft module will provide the final squad, while the match module will turn those drafted players into an official match lineup and a live, rule-aware cricket scorecard.

The correct relationship is:

> **Drafted squad → match squad selection → playing XI → toss → innings → ball-by-ball events → derived scorecard → approved result → points table and history.**

The scorecard must not be maintained by manually editing totals. Every score should be derived from the recorded deliveries and validated against the selected players, innings rules, legal-ball rules, wickets, extras, and match state.

This plan uses the MCC Laws of Cricket and format-specific ICC playing conditions as the rule baseline. MCC Law 21 defines no-ball scoring and legal-ball behavior, while MCC Law 22 defines wide scoring and legal-ball behavior [1] [2] [3]. ICC playing conditions vary by format, so the application must use a versioned tournament rule profile rather than assuming every competition follows international T20, ODI, or Test rules [4].

## 1. Match formats and rule profiles

The first release should support a configurable **limited-overs format**, with T20 as the default for the local tournament. The same engine should be able to support shorter community matches and ODI-style matches by changing a rule profile.

| Rule | Recommended default | Configurable? |
|---|---:|---|
| Innings per side | 1 | Yes |
| Overs per innings | 20 | Yes |
| Playing XI | 11 | Yes |
| Maximum wickets | 10 | Yes |
| Legal balls per over | 6 | Yes, for special formats |
| No-ball penalty | 1 | Yes by rule profile |
| Wide penalty | 1 | Yes by rule profile |
| Tie handling | Super over or declared tie | Yes |
| No-result handling | Points/rules profile | Yes |
| DLS interruption method | Future phase | Yes, disabled initially |
| Powerplays | Optional metadata/rule schedule | Yes |
| Squad substitutes | Configurable | Yes |

The system should store `rule_profile_id` and `rule_profile_version` on every match. If a competition’s rules change in the future, historical scorecards remain tied to the rule version used at the time.

## 2. Roles and match operations

The live scoring module requires more specific permissions than the existing draft roles.

| Role | Match responsibilities |
|---|---|
| Tournament manager | Configure format, fixtures, match rules, and match lifecycle |
| Match scorer | Select delivery outcome and submit ball-by-ball events |
| Match controller | Correct/undo deliveries, manage innings, start/end match, handle interruptions |
| Result approver | Approve the final result and publish it to standings |
| Captain | Submit playing XI, confirm lineup, view team scorecard |
| Player | View own match selection and published performance |
| Public viewer | Read-only live scorecard and published result |

A scorer must not be able to silently change an approved result. A match controller can correct an event, but every correction requires a reason and an audit record. A result approver is the final authority for standings impact.

## 3. Match lifecycle

The scheduled fixture and the live scoring session should be related but conceptually separate. A `fixture` represents the planned match; a `match` represents the operational scoring record created from that fixture.

| Match state | Allowed actions |
|---|---|
| scheduled | Confirm teams, venue, date, and rule profile |
| squad_selection | Build squad from drafted players and submit playing XI |
| lineup_pending | Await captain/admin confirmation |
| toss_pending | Record toss winner and bat/bowl decision |
| live | Record deliveries and live events |
| innings_break | Close first innings and prepare chase/next innings |
| completed | Match has ended; result awaits approval |
| result_pending | Scorecard submitted for review |
| approved | Result is official and affects standings |
| rejected | Result returned for correction |
| abandoned | Match stopped without official result |
| cancelled | Match removed from competition |

No scoring event should be accepted unless the match is in `live` state and the innings is active. No public result should be published before approval.

## 4. Draft-to-match squad flow

### 4.1 Create match from fixture

When an admin opens a scheduled fixture, the system loads the two teams and their approved, selected draft players. The match stores a snapshot of the player names, roles, team names, and draft pick references so later profile edits cannot change historical scorecards.

The system must reject match creation if:

- A fixture team is missing.
- A team does not belong to the tournament.
- The draft has not reached its configured completion condition.
- The player is not an approved tournament player.
- The player was not selected for that team.
- A player is duplicated across the two match squads.

### 4.2 Match squad and playing XI

Create a match squad from the draft-selected players. Captains may propose the playing XI from their drafted squad; the match controller or tournament manager approves it. For a smaller community tournament, the admin may directly choose the XI.

Each match player record should include:

- `match_id`.
- `team_id`.
- `tournament_player_id`.
- `draft_pick_id`.
- Snapshot player name and playing role.
- `selection_type`: squad, playing_xi, substitute, reserve.
- `batting_order`.
- `is_captain`.
- `is_wicketkeeper`.
- Availability/status.
- Approved timestamp and approving user.

The playing XI must be locked before the toss. After the toss or match start, changes require an exceptional replacement workflow and an audit reason.

### 4.3 Toss

The match controller records:

- Toss winner.
- Decision: bat or field.
- Toss time.
- Official who recorded it.

The system then determines the first innings batting team and fielding team. A toss must exist before the match moves from `toss_pending` to `live`.

## 5. Scorecard data model

### 5.1 Matches

Create a `matches` table connected to the fixture:

- `id`, `fixture_id`, `tournament_id`, `division_id`, `season_id`.
- `rule_profile_id`, `rule_profile_version`.
- `status`.
- `toss_winner_team_id`, `toss_decision`.
- `started_at`, `completed_at`, `approved_at`.
- `current_innings_id`.
- `revision` for live polling.
- `last_event_at`.
- `created_by`, `updated_by`.

### 5.2 Innings

Create a `match_innings` table:

- `id`, `match_id`, `innings_number`.
- `batting_team_id`, `bowling_team_id`.
- `status`: pending, live, break, completed, declared, forfeited.
- `target_runs` nullable.
- `maximum_overs`.
- `total_runs`, `wickets`, `legal_balls` as cached values.
- `completed_reason`: all_out, overs_complete, target_reached, declaration, chase_ended, admin_end.
- `started_at`, `completed_at`.

Cached totals are for fast display only. They must be rebuildable from deliveries.

### 5.3 Delivery events

Create a `match_deliveries` table as the source of truth:

- `id`, `match_id`, `innings_id`.
- `over_number`, `ball_number`, `sequence_number`.
- `striker_id`, `non_striker_id`, `bowler_id`.
- `runs_off_bat`.
- `wides`, `no_balls`, `byes`, `leg_byes`, `penalty_runs`.
- `total_runs`.
- `is_legal_delivery`.
- `wicket_id` nullable.
- `commentary`.
- `recorded_by`, `recorded_at`.
- `revision`, `voided_at`, `void_reason`.

A delivery should be append-only after it is accepted. Corrections should void the old event and append a corrected event, rather than silently changing history.

### 5.4 Wickets and dismissals

Create a `match_wickets` table or a normalized wicket record related to a delivery:

- `dismissed_player_id`.
- `dismissal_type`.
- `credited_bowler_id` nullable.
- `fielder_id` nullable.
- `fielder_two_id` nullable for run-out/assist cases.
- `runs_completed` where relevant.
- `is_valid_wicket`.
- `review/notes`.

Supported initial dismissal types should include bowled, caught, caught-and-bowled, lbw, run-out, stumped, hit-wicket, retired-hurt, retired-out, obstructing-the-field, hit-the-ball-twice, timed-out, and absent-hurt/absent. The UI should only show valid dismissal choices for the selected delivery type.

### 5.5 Batting and bowling statistics

Create cached innings-level statistics tables for fast scorecard display:

`innings_batting_stats` should contain player, batting position, runs, balls, fours, sixes, strike rate, dismissal type, dismissed by, fielder, and status.

`innings_bowling_stats` should contain player, overs, maidens, runs conceded, wickets, no-balls, wides, economy, and legal-ball count.

These values must be recalculated from delivery events after every accepted delivery and after every correction. They must never be trusted as the only source of truth.

## 6. Cricket scoring rules to enforce

### 6.1 Runs and extras

Every delivery must distinguish runs off the bat from extras. The scorecard should display:

| Category | Meaning |
|---|---|
| Batter runs | Runs credited to striker |
| Byes | Runs completed without bat contact, not charged to bowler |
| Leg byes | Runs from permitted contact with batter’s person, not charged to bowler |
| Wides | Wide penalty plus any additional wide runs, charged to bowler |
| No-balls | No-ball penalty plus delivery runs, with separate batter/extra treatment |
| Penalty runs | Administrative or law-based penalty runs |
| Total | Sum of all delivery components |

MCC Law 21.15 gives the no-ball a one-run penalty, Law 21.17 says the no-ball does not count as one of the over, and Law 21.18 limits the dismissals possible on a no-ball [2]. MCC Law 22.6 gives the wide a one-run penalty, Law 22.7 records additional wide runs and charges them to the bowler, and Law 22.8 says the wide does not count as one of the over [3].

### 6.2 Legal balls and overs

The application should track `legal_balls`, not only a decimal overs string. The display format should derive from legal balls: `12.0` means 72 legal balls and `12.4` means 76 legal balls, not a decimal mathematical value.

A wide and no-ball increase the innings total but do not increase the legal-ball count. The over completes after six legal deliveries, after which the strike changes according to cricket rules and a new bowler must be selected.

### 6.3 Batting state

The engine must maintain striker and non-striker. It must handle:

- Odd/even completed runs.
- Boundary four and six behavior.
- Strike rotation at over completion.
- No-ball and wide continuation.
- Wicket replacement and incoming batter.
- End of over state.
- Retired hurt and replacement constraints.
- Target chase completion.

### 6.4 Bowling state

The engine must track the bowler’s legal balls, overs, maidens, runs conceded, wickets, wides, and no-balls. Byes and leg-byes should not be charged as bowler runs. The match rule profile should control maximum overs per bowler, such as four overs in a standard T20-style match.

### 6.5 Innings end conditions

An innings ends when:

- The batting team reaches the target.
- The team loses the configured maximum wickets.
- The configured maximum overs are completed.
- The captain/admin declares the innings where the format allows it.
- The match is abandoned, forfeited, or ended by an authorized controller.

The system should automatically suggest the end of an innings but require confirmation for administrative end actions.

## 7. Live scorer workflow

The match controller opens the live scoring room. The layout should show the current innings, score, wickets, overs, target, current batters, current bowler, recent balls, over summary, and action controls.

A scorer records a delivery through a simple interaction flow:

1. Select runs off bat: 0, 1, 2, 3, 4, 5, 6.
2. Select extra: wide, no-ball, bye, leg-bye, penalty.
3. Select wicket if applicable.
4. Select dismissal type and dismissed player.
5. Select fielder if required.
6. Add optional commentary.
7. Preview score impact.
8. Confirm delivery.

The server recalculates all totals inside a transaction, locks the current innings row, increments the match revision, appends the delivery, updates cached statistics, and returns the new state. The UI should not optimistically publish an unconfirmed score.

The scorer should have quick buttons for common deliveries, while an advanced dialog handles unusual events. The match controller should have **Undo last ball**, **Correct event**, **End over**, **End innings**, and **Pause scoring** controls.

## 8. Public live match center

The public page should provide a broadcast-style scorecard without exposing private controls. It should update through the existing revision-based polling mechanism initially, with WebSockets considered later when traffic requires it.

The live public display should show:

- Team names and logos.
- Current score and wickets.
- Overs completed.
- Target and required runs.
- Current run rate and required run rate.
- Current striker and non-striker.
- Current bowler figures.
- Recent balls and over-by-over timeline.
- Full batting card.
- Full bowling card.
- Extras breakdown.
- Fall of wickets.
- Partnerships.
- Match status and result.
- “Last updated” server time.

It must not show scorer controls, draft admin actions, private player contact data, unpublished corrections, or internal audit metadata.

## 9. Captain and admin match pages

### Captain page

The captain should see the team’s drafted squad, submit or confirm the playing XI, view the toss result, follow the live scorecard, and view team-specific batting/bowling performance after publication. Captain access must be restricted to fixtures involving the captain’s assigned team.

### Admin/match-controller page

The admin page should include match setup, lineup approval, toss, live scoring, correction history, scorecard preview, result submission, and result approval queue. It should show an explicit state banner so a user cannot confuse an editable scorecard with an approved result.

## 10. Result approval and standings integration

When an innings or match is completed, the scorecard moves to `result_pending`. The result approver sees a full scorecard and a standings impact preview. Approval creates an immutable approved result version.

After approval:

1. The match result becomes public.
2. The standings service applies points and statistics exactly once.
3. The fixture becomes completed.
4. Team history receives the match result and player performance snapshot.
5. Reports and PDFs become available.

If the result is corrected later, the system should create a new result revision, reverse the previous standings contribution, apply the corrected contribution, and record the reason. No approved result should be edited silently.

## 11. Security and concurrency

Live scoring is a high-risk mutation flow. Every delivery request should include CSRF protection, authenticated scorer permission, match assignment validation, request throttling, and a client request identifier for idempotency.

The server should lock the innings and match rows while applying a delivery. The `revision` number should reject stale clients. If two scorers submit simultaneously, one should succeed and the other should receive a conflict response with the latest state rather than duplicating a ball.

A delivery request must be rejected if:

- The match is not live.
- The innings is not active.
- The selected striker, non-striker, or bowler is not in the approved playing XI.
- The bowler has exceeded the format’s maximum overs.
- The event violates legal-ball or wicket rules.
- The request is a duplicate.
- The innings is already complete.
- The client revision is stale.

Every accepted, corrected, voided, innings-ended, result-submitted, and result-approved action must be audited.

## 12. Corrections and recovery

The system should support safe operational recovery without deleting historical truth.

- Undo last ball: creates a void record and restores derived stats.
- Correct older ball: creates a correction event linked to the original.
- Rebuild scorecard: recalculates all innings totals from non-voided deliveries.
- Rebuild standings: recalculates all approved match results.
- Abandon match: preserves score and reason but prevents standings impact unless the rule profile says otherwise.
- Interrupted match: store interruption reason, overs completed, and restart instructions.
- Browser disconnect: scorer reloads from server revision; no local score becomes authoritative.

Offline scoring can be considered later. The initial release should prefer correctness over offline complexity.

## 13. Reports and PDFs

Add these report types to the existing role-aware PDF system:

| Report | Admin | Captain | Public |
|---|---|---|---|
| Full match scorecard | Yes | Own team matches | Published matches |
| Ball-by-ball commentary | Yes | Own team matches | Published matches |
| Batting scorecard | Yes | Own team matches | Published matches |
| Bowling scorecard | Yes | Own team matches | Published matches |
| Partnership report | Yes | Own team matches | Published matches |
| Player performance | Yes | Own team players | Published players |
| Correction/audit log | Yes | No | No |

PDFs should use tournament logo and name, match title, venue, date, team logos, scorecard sections, and actual report name. They should not contain “Admin report”, “Captain report”, or “Public report” labels.

## 14. Implementation phases

### Phase 0 — Rules and match configuration

Add rule profiles, format selection, overs, innings count, playing XI size, maximum overs per bowler, result rules, tie handling, and versioning. Confirm the default local format as T20-style, one innings per side, 20 overs, playing XI of 11.

**Exit gate:** A tournament can select a rule profile and the profile is stored with future fixtures/matches.

### Phase 1 — Fixture-to-match and squad selection

Create match records from fixtures, load drafted players, create match squad snapshots, submit/approve playing XI, record toss, and lock lineups.

**Exit gate:** A match cannot start without two valid drafted squads, approved XI, and toss data.

### Phase 2 — Match and innings backend

Create matches, innings, deliveries, wickets, match squad records, cached batting/bowling statistics, revision handling, transactions, and rebuild services.

**Exit gate:** A test innings can be rebuilt exactly from delivery events.

### Phase 3 — Scorer control room

Build the scorer UI, quick delivery buttons, extras/wicket dialogs, commentary, undo, correction, innings controls, and conflict handling.

**Exit gate:** A scorer can record a complete sample innings and every scorecard section matches the delivery ledger.

### Phase 4 — Public live scorecard

Build public match center, live score, batsmen, bowler, recent balls, innings tabs, scorecard tables, and server-synchronized polling.

**Exit gate:** A public viewer sees updates without refresh and cannot access scorer/admin actions.

### Phase 5 — Result approval and standings

Add result submission, approval/rejection, result revisioning, points calculation, NRR where enabled, standings, reports, and team-history updates.

**Exit gate:** Only approved results affect standings and corrections preserve the full history.

### Phase 6 — Testing, pilot, and archive integration

Add concurrency tests, format-rule tests, security tests, scorecard PDF tests, browser smoke tests, sample tournament pilot, backups, deployment checklists, and archive freeze behavior.

## 15. Essential test cases

The implementation must test normal scoring and difficult cricket events.

| Test group | Required cases |
|---|---|
| Basic scoring | Dot ball, singles, doubles, boundary four, six |
| Extras | Wide, multiple wide runs, no-ball, byes, leg-byes, penalty runs |
| Legal balls | Six legal balls, repeated wides/no-balls, over completion |
| Strike | Odd runs, even runs, over-end strike change, boundary behavior |
| Wickets | Bowled, caught, lbw, run-out, stumped, hit-wicket, retired hurt |
| Invalid wickets | No-ball dismissal restrictions, wide dismissal restrictions |
| Innings | All out, overs complete, target reached, abandoned |
| Bowler | Maximum overs, wides/no-balls charged, byes not charged |
| Scorecard | Batter, bowler, extras, partnerships, fall of wickets, totals |
| Corrections | Undo last ball, correct old event, rebuild totals |
| Concurrency | Stale revision, duplicate request, simultaneous scorer submissions |
| Authorization | Scorer, controller, captain, approver, public boundaries |
| Results | Submit, reject, approve, correction, standings idempotency |
| Reports | Role scope, public redaction, PDF branding |

## 16. Recommended first match release

Do not begin with full international-level cricket including DLS, DRS, ball-by-ball wagon wheels, and complex playing-condition exceptions. The correct first release is:

- T20-style configurable limited-overs match.
- One innings per side.
- Draft-selected squads.
- Approved playing XI.
- Toss.
- Ball-by-ball runs and extras.
- Main dismissal types.
- Legal-ball and over engine.
- Batting and bowling scorecards.
- Live public scorecard.
- Admin/scorer correction and undo.
- Result approval.
- Points-table integration.
- Match scorecard PDF.

After this works with a real pilot match, add advanced formats, DLS, super overs, substitutions, player statistics, and detailed fielding analytics.

## 17. Product decisions required before coding

The following decisions should be confirmed:

| Decision | Recommended default |
|---|---|
| Initial format | T20-style limited overs |
| Overs | 20 per side |
| Playing XI | 11 |
| Innings | One per side |
| Scoring mode | Ball-by-ball, server-authoritative |
| First operator | Dedicated scorer role |
| Final approval | Separate result approver/admin |
| No-ball/wide rules | MCC baseline with tournament profile overrides |
| Super over | Future phase, not initial release |
| DLS | Future phase, not initial release |
| Offline mode | Future phase, server remains authoritative initially |
| Public updates | Existing revision-based polling initially |
| Result correction | Immutable revision/correction ledger |

## References

[1]: https://www.lords.org/mcc/the-laws "MCC Laws of Cricket"
[2]: https://www.lords.org/mcc/the-laws/no-ball "MCC Law 21: No ball"
[3]: https://www.lords.org/mcc/the-laws/wide-ball "MCC Law 22: Wide ball"
[4]: https://www.icc-cricket.com/about/cricket/rules-and-regulations/playing-conditions "ICC Playing Conditions"
