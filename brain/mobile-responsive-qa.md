# Responsive QA — 22 November 2026

## Scope

Mobile-only CSS for hero, award card, home sections, navigation touch target,
public page headings/forms/modals, schedule grid, and chatbot presentation.
Chatbot endpoints, model, prompt, request payloads, and session ID are unchanged.

## Checks performed

- `php artisan view:cache`: passed.
- `git diff --check`: passed.
- Edge headless through native Chrome DevTools Protocol; no dependencies installed.
- Before/after fixtures use Git HEAD versus working-tree CSS and chatbot template.
- Hero fixture uses representative original fallback text and a placeholder image;
  these are component checks, not screenshots of populated production pages.

| Viewport | Hero horizontal overflow | Chat input/send inside viewport | Long messages scroll vertically | History retained |
| --- | --- | --- | --- | --- |
| 320 × 700 | None | Pass | Pass | Pass |
| 375 × 812 | None | Pass | Pass | Pass |
| 390 × 844 | None | Pass | Pass | Pass |
| 430 × 932 | None | Pass | Pass | Pass |
| 768 × 1024 | None | Pass | Pass | Pass |
| 844 × 390 landscape | None | Pass | Pass | Pass |
| 1024 × 768 | None | Pass | Pass | Pass |
| 1366 × 900 | None | Pass | Pass | Pass |
| 1440 × 900 | None | Pass | Pass | Pass |
| 1920 × 1080 | None | Pass | Pass | Pass |

Mobile long-message test: 30 alternating bot/user messages with long unbroken URLs.
No horizontal message overflow below 1024px. Shrinking viewport height by 320px
(minimum 240px) keeps input/send visible; this simulates available space, not a real OS keyboard.
At desktop breakpoints, pre-existing unbroken-text overflow remains; desktop CSS was preserved.

## Desktop comparison

- Hero screenshot bytes identical at 1024, 1366, 1440, and 1920px.
- Chat screenshot bytes identical at 1024, 1366, and 1920px on final run.
- Chat at 1440px not consistently byte-identical between runs; no claim of full pixel parity.
- Chat dimensions remain 380 × 600px at all tested desktop widths.
- Animations/transitions disabled only in screenshot harness for comparison.

## Limits / remaining verification

Laravel local HTTP server timed out. Full-page rendered checks (public, authenticated,
admin), original uploaded photos/fonts, real device safe areas, real iOS/Android keyboards,
and live AI replies remain unverified. Do not treat component fixture success as site-wide approval.
No API calls made by chatbot QA; messages were inserted into fixture DOM.

Temporary harness, JSON metrics, and screenshots:
`C:\Users\ARFAZU~1\AppData\Local\Temp\opencode\component-qa.mjs`
and `component-results.json`, `before-*-hero.png`, `after-*-hero.png`, `*-chat.png` alongside it.
Harness emitted results but process shutdown timed out on final run.
