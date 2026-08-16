# Cricket scoring research notes

## Authoritative sources

1. MCC Laws of Cricket: https://www.lords.org/mcc/the-laws
2. MCC Law 21, No ball: https://www.lords.org/mcc/the-laws/no-ball
3. MCC Law 22, Wide ball: https://www.lords.org/mcc/the-laws/wide-ball
4. ICC Playing Conditions: https://www.icc-cricket.com/about/cricket/rules-and-regulations/playing-conditions

## Relevant implementation findings

- MCC Law 21.15 states that a no-ball carries a one-run penalty, and Law 21.17 states that a no-ball does not count as one of the over.
- MCC Law 21.16 distinguishes the no-ball extra from runs credited to the striker and states that the no-ball penalty is debited against the bowler.
- MCC Law 21.18 restricts dismissals on a no-ball to hit the ball twice, obstructing the field, and run out.
- MCC Law 22.6 states that a wide carries a one-run penalty. Law 22.7 records additional completed runs or boundary allowance as wides and debits them against the bowler.
- MCC Law 22.8 states that a wide does not count as one of the over.
- MCC Law 22.9 restricts dismissals on a wide to hit wicket, obstructing the field, run out, and stumped.
- The scorecard model therefore needs separate fields for batter runs, bye runs, leg-bye runs, no-ball extras, wide extras, penalty runs, total delivery runs, legal-ball count, wickets, and dismissal metadata.
- ICC playing conditions are format-specific. The product should not hard-code international Test/ODI/T20I rules into every tournament. Each tournament should select a format/rule profile and store its rule version.
- The first release should support match-level configuration for overs, innings, squad/playing-XI size, and result rules, with a versioned rule profile. Ball-by-ball scoring should be the source of truth; scorecard totals should be derived and rebuildable.
