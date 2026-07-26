# Admin98 → WebHemi product integration

Integration plan for bringing the [webhemi-admin98](https://github.com/Gixx/webhemi-admin98) Win98 tech demo into the real WebHemi product (`@webhemi/ui` + PHP AssetMapper).

## Fixed decisions

Canonical detail: **[Admin98_Integration_Contract.md](./Admin98_Integration_Contract.md)** (Phase 0 ADR). Summary:

- **Target package:** all product UI lives in [`webhemi-ui`](../../webhemi-ui/) (`@webhemi/ui`). [webhemi-admin98](https://github.com/Gixx/webhemi-admin98) remains a **reference / sandbox** until Storybook parity; not a separate NPM dependency. UX parity matters; byte-identical markup does not.
- **Product concept:** Admin theme (Win98) + swappable, **self-contained** frontend themes. No shared UI kit — each theme owns tokens, styles, and components. Thin `src/lib/` helpers (e.g. `cn`) are OK; UI atoms are not.
- **Current trees are throwaway / relocatable:** today’s `src/admin/**` is a full rewrite target (not a restyle). Today’s `src/shared/components/*` move into `themes/default` (or are rewritten there) as Default work proceeds — Admin never imports them.
- **Admin98 assets are admin-only:** icons, fonts, chrome/product styles, shell behavior serve **only** the Admin theme. Frontend themes do not import Admin chrome.
- **Theme scope:** `[data-wh-theme="admin"]` on `<html>` only. `body.dashboard` / `.wh-admin` are **not** product contracts (sandbox may still use `body.dashboard` as a demo shell).
- **Markup:** 98-compatible class names (`.window`, `.field-row`, …); no `wh-` rename wave initially.
- **Delivery path:** unchanged — `npm run build` → `dist/` → PHP `bin/sync-ui.sh` / `composer run sync-ui` → AssetMapper + `react_component(...)` ([Local development](../local-dev.md)).
- **Order:** styles → chrome atoms (Storybook) → product layout bricks → 1–2 real surfaces (Login + Control Panel) → desktop shell MVP → remaining admin pages into windows.

```mermaid
flowchart TB
  subgraph sandbox [webhemi-admin98]
    Catalog[catalog.html]
    ChromeSCSS[chrome SCSS]
    ProductSCSS[product SCSS]
    ShellJS[desktop shell JS]
  end
  subgraph ui [webhemi-ui]
    Tokens[admin tokens scoped]
    Atoms[React chrome atoms]
    Bricks[React product bricks]
    Shell[AdminDesktop shell]
    Pages[Login / ControlPanel / ...]
    SB[Storybook Admin]
  end
  subgraph php [webhemi-php]
    Sync[sync-ui dist]
    Twig[react_component]
  end
  Catalog --> Atoms
  ChromeSCSS --> Tokens
  ProductSCSS --> Bricks
  ShellJS -.->|port behavior| Shell
  Atoms --> SB
  Bricks --> Shell
  Shell --> Pages
  Pages --> Sync
  Sync --> Twig
```

---

## Phase 0 — Integration contract (1–2 days) — **done**

**Work:** ADR under hub `docs/plan/`: layers, markup contract, theme-scope rule, theme ownership, what stays in the sandbox; PHP `data-wh-theme` wiring.

**Outputs:**
- Contract: [Admin98_Integration_Contract.md](./Admin98_Integration_Contract.md).
- Chrome markup = 98-compatible class names (`.window`, `.field-row`, `aria-label` title controls) — no `wh-` prefix rename initially.
- Admin CSS applies only under `[data-wh-theme="admin"]` (not `body.dashboard` / `.wh-admin`).
- Frontend themes do **not** import Admin chrome SCSS; no shared UI layer.
- PHP base layout: `<html data-wh-theme="admin">` (login + admin; site themes override later).

**Hard parts (resolved in ADR):**
- Storybook toolbar already uses `data-wh-theme` ([`.storybook/preview.tsx`](../../webhemi-ui/.storybook/preview.tsx)) — product scope aligns to it.
- Trade dress / IP intentionally out of scope; this plan is architecture only.

---

## Phase 1 — Move the style system into `webhemi-ui`

**Work:** copy / adapt:
- tokens (`assets/style/abstract/tokens.css` in admin98 + `@theme`)
- bevel mixins + `chrome/` partials
- product partials (layouts, scrollbar skin, desktop, toolbar) — initially as **CSS**, without React
- icon / font assets

Suggested layout:

```text
webhemi-ui/src/admin/styles/
  tokens.css
  chrome/          # ported SCSS
  product/         # shell + layouts
  entry.css        # theme-scoped import
```

Build: extend the current `build:css` entry in [`webhemi-ui/package.json`](../../webhemi-ui/package.json); Storybook/Vite and the library build must compile Sass the same way (admin98 lesson: compile SCSS via Vite, not via Tailwind CSS `@import`).

**Hard parts:**
- **Global `button` / `input` selectors** — scoping is mandatory, or Default frontend stories become Win98.
- **`cssMinify: false` / `(not (hover))` media** — lightningcss conflict; handle in the UI build too.
- **Product `.sunken-panel` silver override** vs chrome white — Storybook decorator: “chrome only” vs “product”.
- Preflight: off in admin98; shared `base.css` must be overridden or omitted under the admin entry.

**Done when:** in Storybook with Admin theme, catalog samples visually approximate `catalog.html` (smoke).

---

## Phase 2 — React chrome atoms + Storybook Atoms

**Work:** thin React wrappers per catalog section that **preserve the DOM contract** (e.g. checkbox: `input` immediately before `label`). Story sources: admin98 `catalog.html` examples + its atom catalog plan hierarchy.

Priority: Button, TextBox, Checkbox, Radio, Select, FieldRow/GroupBox, Window/TitleBar/StatusBar, Tabs, TreeView, SunkenPanel, Table, Progress, Slider.

Stop using old [`src/shared/components/*`](../../webhemi-ui/src/shared/components/) for admin (and do not keep a shared UI kit long-term — Default owns its atoms under `themes/default`). Win98 React atoms live under `src/admin/` only. Frontend theme components are unrelated; no shared Button/Input/Checkbox with Admin.

**Hard parts:**
- **Dual source of truth:** `catalog.html` vs CSF stories — Storybook is canonical; sandbox catalog stays regression / manual reference until parity, then optional.
- **Adjacency CSS** (`input + label`) — FormField-style wrappers that break `+` selectors are forbidden; prefer exact DOM / fragments.
- **Tabs nested `.window[role=tabpanel]`** — separate `TabPanel` component name, not `Window`.
- Interactive table: port admin98 `tableView.js` as a React hook / small module.

**Done when:** Admin / Atoms sidebar is populated; a11y addon smoke OK; switching theme leaves Default stories untouched.

---

## Phase 3 — Product bricks (layouts, scrollbar)

**Work:** React composition per admin98 `structure.html`:
- `DialogWindow` (dialog-panel-layout)
- `IconPanelWindow`
- `WizardWindow`
- `HeadingPanelWindow`
- `ScrollableRegion` (+ admin98 `scrollbar.js` port)
- later: `DesktopIcon`, `Taskbar`, `StartMenu` (storiable static even without shell)

**Hard parts:**
- Scrollbar is strongly DOM-coupled (viewport + `.sb-*`) — Storybook decorator + `useEffect` mount required.
- Layout negative-margin / groove rules are fragile with flex/grid — visual snapshot or manual checklist vs the sandbox demo.

**Done when:** Bricks / Components stories cover the four pane layouts.

---

## Phase 4 — First product surfaces (hybrid MVP in PHP)

**Work:** replace the current modern admin look with Win98 dialogs, **without** a full desktop yet:

1. **Login** — `LoginPage` → `DialogWindow` + form atoms; keep the same props (`action`, CSRF, error) so Twig ([`templates/security/login.html.twig`](../../webhemi-php/templates/security/login.html.twig)) does not break.
2. **One internal surface** — e.g. Settings or Control Panel `IconPanelWindow` (Sites/Hosts/… icons) linking to existing routes / opening windows later.

Export via [`admin/index.ts`](../../webhemi-ui/src/admin/index.ts) + PHP controller re-exports with unchanged names (`LoginPage`, …).

**Hard parts:**
- AssetMapper only sees built `dist` — local loop: UI build + `sync-ui` (or `make up` watch) is required.
- Login is not in the desktop shell today; full-bleed dialog vs `body.dashboard` background — use teal desktop background on login, no taskbar.
- Old `AdminLayout` / Sidebar / TopBar **live in parallel** until all pages migrate — avoid a half-migrated layout on one page.

**Done when:** `https://…/login` shows a Win98 dialog after sync; CSRF / error messaging still works.

---

## Phase 5 — Desktop shell MVP (React)

**Work:** port sandbox shell behavior to React (do not iframe the demo):
- window z-order, active/inactive title-bar
- drag (title-bar), resize handles
- minimize / maximize / close + taskbar task buttons
- Start menu (Control Panel launch)
- icon double-click → window
- position / size persistence (`localStorage`) — product key namespace (`webhemi.admin.desktop…`)

Behavior sources: admin98 `windowHandler.js`, `desktop.js`, `taskbarHandler.js`, `iconHandler.js`.

New top-level: e.g. `AdminDesktop` / `AdminShell`, mounted from PHP `AdminDashboard` (and later other pages).

**Hard parts:**
- **Largest effort.** Pointer capture, grid-snap, cascade, bounded resize — regression-sensitive; keep the sandbox demo as a side-by-side checklist.
- Nested tab `.window` vs shell `.window` — use `data-shell-window` / context; do not naively `closest('.window')`.
- Symfony multi-page navigation vs single-page desktop: MVP = **one** React mount under `/admin`, inner “windows” = view state (no full reload per icon). Login may stay a separate URL.
- Zero-Node prod: all shell JS must ship inside the `@webhemi/ui` bundle; no extra runtime script.

**Done when:** demo-like: draggable windows, taskbar, Start → Control Panel, positions survive refresh.

---

## Phase 6 — Move existing admin pages in

**Work:** put `SitesPage`, `HostsPage`, list views, etc. into Win98 windows / `HeadingPanel` + table / form layouts. Routing:
- short term: shell state + deep link query (`?window=sites`)
- Symfony routes or wrapper Twigs that mount the same `AdminShell` with an initial window prop

**Hard parts:**
- Data fetch / form POST may still be server-centric — keep Twig+React props, or move gradually to API.
- Modal vs Window: replace old `Modal` with native `.window` dialogs.
- Auth / flash messages: status-bar or small message-dialog brick.

**Done when:** current admin features are reachable from the shell; old AdminLayout can be removed.

---

## Phase 7 — Cleanup and packaging

**Work:**
- Delete unused legacy admin components/stories; finish relocating former `shared` UI into `themes/default` (or remove if unused).
- Storybook sidebar: **Admin / Atoms|Bricks|Components|Foundations** + **Themes / Default** (frontend).
- Consider `dist` CSS split: `styles.css` = admin (default export for PHP); separate frontend theme CSS later if the site surface becomes React.
- Sandbox: README pointer “canonical stories in webhemi-ui”; `catalog.html` optional visual regression or archive.
- Hub README / architecture doc: Admin Theme = Win98 owned chrome; themes are self-contained.

**Hard parts:**
- Public NPM breaking visual change (`@webhemi/ui`) — `0.x` is fine, but changelog is required.
- Chromatic / visual tests: introduce on chrome atoms before the shell makes diffs noisy.

---

## Suggested timeline (indicative)

| Phase | Time (indicative) | Main risk |
|-------|-------------------|-----------|
| 0–1 | 3–5 days | Theme-scope, Sass/Tailwind build parity |
| 2 | 1–2 weeks | DOM contract in React, story backlog |
| 3 | 3–5 days | Scrollbar + layout pixel fidelity |
| 4 | 3–5 days | PHP sync + Login parity |
| 5 | 1.5–3 weeks | Window manager fidelity |
| 6–7 | ongoing | Page migration, breaking cleanup |

---

## Explicitly out of scope

- Making frontend (site) themes Win98
- Sharing admin98 chrome/icons/scripts with frontend themes (admin-only by design)
- Replacing `webhemi-js` / Payload admin
- Publishing admin98 as its own NPM package
- Reintroducing the npm `98.css` dependency

---

## Phase checklist

- [x] Phase 0 — Integration contract
- [ ] Phase 1 — Styles into `webhemi-ui` (scoped)
- [ ] Phase 2 — React chrome atoms + Storybook
- [ ] Phase 3 — Product layout bricks + ScrollableRegion
- [ ] Phase 4 — Win98 Login + one Control Panel surface via PHP
- [ ] Phase 5 — React AdminDesktop shell
- [ ] Phase 6 — Migrate Sites/Hosts/… into windows
- [ ] Phase 7 — Remove legacy admin UI; docs/changelog; sandbox role update
