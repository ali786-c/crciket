# UI preview findings

## Landing page

The redesigned landing page was visually verified at the preview URL. The premium dark-green hero, lime accent, command-center mock card, high-contrast CTA hierarchy, and three feature cards render correctly above the fold. The layout is responsive in structure and uses the new Cricket Draft OS visual language.

## Public live board

The seeded preview URL `/tournaments/preview-cup/draft/live` currently returns the safe 404 page in the running preview. This is an environment/data-state issue rather than a Blade rendering failure; the 404 page itself renders correctly with the new visual system. The local demo tournament should be re-seeded or the preview database state checked before final live-board verification.

## Restored public live board

After reseeding roles and the demo tournament, `/tournaments/preview-cup/draft/live` renders correctly. The screenshot shows a clear broadcast hierarchy: current team and pick in a dark green pitch panel, lime countdown, scoreboard metrics, and a readable pick-by-pick history table. The active pick and pending pick states are visually distinct.

## Final landing verification

The landing page was reloaded after the Blade fixes and renders successfully. The hero, premium green/lime visual language, CTA hierarchy, live-board mock panel, feature cards, and footer all appear clean in the preview viewport. The page is ready for continued responsive and authenticated-screen QA.

## Authentication screen

The redesigned login screen was visually verified in the preview. The split layout presents the brand and draft-day message on a dark pitch panel, while the form sits in a bright rounded surface with clear labels, a strong primary CTA, and a compact registration prompt. The form hierarchy and spacing are readable at the desktop preview width.

## Registration screen

The registration page was visually verified and matches the login split-panel experience. The form has a clear Player onboarding label, concise supporting copy, full-width fields, and a strong Register as player CTA. The guest shell maintains the dark pitch panel and bright form surface consistently.

## Mobile viewport QA

Headless Chromium screenshots were rendered at 390×844 for the landing page and public live board. The landing page has no visible horizontal overflow: the topbar compresses to the icon plus CTAs, hero copy wraps cleanly, CTAs become full-width stacked buttons, feature signals wrap, and the live-board mock card continues below the fold naturally. The public board also stacks the header, pitch panel, scoreboard, and history sections cleanly. The expired state shows a readable 00:00 timer and admin-action message without clipping.

## Mobile authentication QA

At 390×844, the login page collapses to the form panel cleanly with the brand centered, readable field widths, full-width primary CTA, and a wrapped registration prompt. The registration page also fits the first screen without horizontal overflow: fields stack with comfortable touch spacing, the onboarding copy stays centered, and the Register as player button remains prominent.

## Live draft center verification

The new `/live-drafts` page was opened in the preview and shows only the currently live `Preview Cricket Cup`. The card uses real data: Lahore location, two teams, Ali Panthers on the clock, 0/2 selected, and a real timer state. The Open live draft action points to the tournament-specific public board.
