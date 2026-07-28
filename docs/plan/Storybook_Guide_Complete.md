# Storybook Guide Complete

Finish the remaining Storybook Guide checklist items in `webhemi-ui` so Development / Testing / Document can be marked done.

## Current state

**Code (steps 1–4):** done.

**Guide UI (step 5):** you mark items in Storybook’s Guide checklist. Code alone cannot tick those boxes.

Already done earlier in the Guide UI: Storybook basics, Group your components, Share, Test your components.

| Guide item | Status |
| --- | --- |
| Change a story with Controls | Ready — mark in UI |
| Check responsiveness with Viewports | Ready — mark in UI |
| Test functionality with interactions | Ready (`play` on IconPanelWindow) — mark in UI |
| Run accessibility tests | Ready — run testing widget, then mark |
| Run visual tests | Ready — Chromatic + secret — mark after CI/local publish |
| Generate a coverage report | Ready — enable Coverage in widget, then mark |
| Automate tests in CI | Ready — workflow updated — mark after green CI (or Mark as complete now) |
| Automatically document your components | Ready — autodocs — visit Docs, then mark |
| Custom content with MDX | Ready — `Introduction` — visit, then mark |

---

## Implementation (done)

### 1. Vitest addon setup

[`webhemi-ui/vite.config.ts`](../../webhemi-ui/vite.config.ts): coverage (v8), `storybookScript`, `optimizeDeps.include` for `storybook/test` + `@testing-library/dom`. Scripts: `test-storybook`, `test-storybook:coverage`. No manual `vitest.setup.ts` (SB 10.3+ auto annotations).

### 2. Interaction story

[`IconPanelWindow.stories.tsx`](../../webhemi-ui/src/admin/bricks/IconPanelWindow/IconPanelWindow.stories.tsx): `play` clicks **Sites**, asserts info + status bar.

### 3. Autodocs + MDX

[`preview.tsx`](../../webhemi-ui/.storybook/preview.tsx): `tags: ['autodocs']`; Docs mode scopes Retro OS theme to the story canvas only. [`src/Introduction.mdx`](../../webhemi-ui/src/Introduction.mdx).

### 4. CI

Split workflows (aligned with [Chromatic GitHub Actions](https://www.chromatic.com/docs/github-actions/)):

- [`ci.yml`](../../webhemi-ui/.github/workflows/ci.yml) — typecheck, lint, build, Playwright + Vitest, `build-storybook`.
- [`chromatic.yml`](../../webhemi-ui/.github/workflows/chromatic.yml) — Chromatic only on `push`, `fetch-depth: 0`, `exitZeroOnChanges: true`.

`package.json`: `"chromatic": "chromatic --exit-zero-on-changes"`. Token via `CHROMATIC_PROJECT_TOKEN` only.

---

## 5. Guide UI checklist (do this in the browser)

Restart Storybook so it picks up MDX / preview / CI-related config:

```bash
cd /home/dev/Projects/WebHemi/webhemi-ui && npm run storybook
```

Open **Guide** in the sidebar, then:

### Development

1. **Controls** — go to `Admin/Bricks/IconPanelWindow` → **Control Panel** → Controls panel → change `width` or `titleIcon` → back to Guide → **Mark as complete** (or it auto-completes).
2. **Viewports** — same story → toolbar **Viewport** → pick a phone size → Guide → mark complete.

### Testing

3. **Interactions** — same story → **Interactions** panel (play runs) → Guide → mark complete.
4. **Accessibility** — sidebar testing widget → enable Accessibility → **Run tests** → Guide → mark complete.
5. **Coverage** — testing widget → enable **Coverage** → Run tests → Guide → mark complete.
6. **Visual tests** — after a Chromatic publish (`Chromatic` workflow on push, or `CHROMATIC_PROJECT_TOKEN=… npm run chromatic`) → Guide → mark complete.
7. **Automate tests in CI** — after `CI` + `Chromatic` workflows are green on push → Guide → **Mark as complete**.

### Document

8. **Autodocs** — open any Admin atom **Docs** tab (e.g. `Admin/Atoms/Button`) → Guide → mark complete.
9. **MDX** — open top-level **Introduction** → Guide → mark complete.

When every row is green, the Guide is finished; you can **Remove from sidebar** if you like.

## Out of scope

- Turning `a11y.test` from `'todo'` to `'error'` (would fail many Retro OS chrome stories until audited).
- Coverage percentage gates.
- Rewriting every story for autodocs quality (global tag is enough for the Guide).
