# Admin98 → WebHemi product integration

Integration plan for bringing the [webhemi-admin98](https://github.com/Gixx/webhemi-admin98) Retro OS tech demo into the real WebHemi product (`@webhemi/ui` + PHP AssetMapper).

## Fixed decisions

Canonical detail: **[Admin98_Integration_Contract.md](./Admin98_Integration_Contract.md)** (Phase 0 ADR). Summary:

- **Target package:** all product UI lives in [`webhemi-ui`](../../webhemi-ui/) (`@webhemi/ui`). [webhemi-admin98](https://github.com/Gixx/webhemi-admin98) remains a **reference / sandbox** until Storybook parity; not a separate NPM dependency. UX parity matters; byte-identical markup does not.
- **Product concept:** Admin theme (Retro OS) + swappable, **self-contained** frontend themes. No shared UI kit — each theme owns tokens, styles, and components. Thin `src/lib/` helpers (e.g. `cn`) are OK; UI atoms are not.
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

## Phase 1 — Move the style system into `webhemi-ui` — **done**

**Work:** copy / adapt:
- tokens (`assets/style/abstract/tokens.css` in admin98 + `@theme`)
- bevel mixins + `chrome/` partials
- product partials (layouts, scrollbar skin, desktop, toolbar) — initially as **CSS**, without React
- icon / font assets

Layout:

```text
webhemi-ui/src/admin/styles/
  tokens.css
  fonts.css
  abstract/        # bevel mixins
  chrome/          # ported SCSS + icons
  product/         # shell + layouts
  entry.scss       # meta.load-css under [data-wh-theme="admin"]
webhemi-ui/src/admin/assets/
  fonts/ icons/    # inlined into dist/index.css (sync-ui copies CSS only)
webhemi-ui/src/styles/
  platform.css     # Tailwind theme+utilities, no Preflight
  entry.js         # Vite CSS entry
```

Build: `vite.css.config.ts` (+ `sass`); Storybook imports the same SCSS entry. `cssMinify: false` for `@media (not (hover))`.

**Hard parts (handled):**
- Global `button` / `input` → nested via `meta.load-css` under `[data-wh-theme="admin"]`.
- No `body.dashboard` contract — product shell styles `body` under the theme scope.
- Preflight omitted (`platform.css`); Default theme owns its body face in `themes/default/styles/tokens.css`.
- Smoke story: `Admin/Foundations/CatalogSmoke` (removed during Phase 3 Storybook cleanup; atom/brick stories are the regression surface).

**Done when:** in Storybook with Admin theme, chrome/product styles load under `[data-wh-theme="admin"]` and match admin98 catalog samples.

---

## Phase 2 — React chrome atoms + Storybook Atoms — **done**

**Work:** thin React wrappers per catalog section that **preserve the DOM contract** (e.g. checkbox: `input` immediately before `label`). Story sources: admin98 `catalog.html` examples + its atom catalog plan hierarchy.

**Delivered under** [`webhemi-ui/src/admin/chrome/`](../../webhemi-ui/src/admin/chrome/):
Button, TextBox/TextArea, Checkbox, Radio, Select, FieldRow/GroupBox, Window/TitleBar/StatusBar, Tabs/`TabPanel`, TreeView, SunkenPanel/FieldBorder, Table + `useTableView`, Progress, Slider.

Storybook: **Admin/Atoms/** + Foundations CatalogSmoke rewritten to use atoms. LoginForm composed from chrome + `dialog-panel-layout` (early Phase 4 surface). Package root exports Admin chrome `Button`/`Checkbox`/`Select` (shared duplicates not re-exported).

**Hard parts (handled):**
- Adjacency: Checkbox/Radio are fragments (`input` + `label`); FieldRow wraps without breaking `+`.
- `TabPanel` ≠ `Window` (same `.window` class, different component).
- Interactive table: `useTableView` ports admin98 `tableView.js`.

**Done when:** Admin / Atoms sidebar populated; Default stories stay on `data-wh-theme="default"` via preview section rule.

---

## Phase 3 — Product bricks (layouts, scrollbar)

**Status:** done (`src/admin/bricks/`, Storybook `Admin/Bricks/*`).

**Delivered:**
- `DialogWindow`, `IconPanelWindow`, `WizardWindow` (`HeadingPanelWindow` removed — rewrite later)
- Custom scrollbar as **chrome capability** (`Scrollable`, `SunkenPanel`/`FieldBorder` `scrollable` prop, `useCustomScrollbar` / `attachCustomScrollbar`) — not a product brick
- `SystemIcon` (product brick; desktop + icon panels; Taskbar / StartMenu deferred to Phase 5)
- `LoginForm` refactored onto `DialogWindow`

**Hard parts (resolved / noted):**
- Scrollbar: chrome owns `.scrollable` / viewport; effect mounts `.sb-*` rails only (`SunkenPanel`/`FieldBorder` `scrollable` or `Scrollable` for layout hosts).
- Layout negative-margin / groove rules — verify visually vs sandbox in Storybook.

---

## Phase 3b — Dynamic `accessKey` (chrome polish)

**Status:** planned (detail: [AccessKey_Dynamize.md](./AccessKey_Dynamize.md)).

**Why here:** chrome atoms and dialog bricks exist; Login / Control Panel (Phase 4) should consume the API instead of hand-rolled `<u>` markup. Deferred from Phase 3 fine-tuning so that work stays a focused chrome change.

**Work:**
- Helper: underline first matching letter (case-insensitive) in plain-string labels/button text
- `Button`: `accessKey` → DOM attribute + auto `<u>` in string children
- `FieldRow`: `accessKey` → attribute on the control; `<u>` on the associated `<label>` text (not on the wrapper `div`)
- Stories + migrate `DialogWindow` stories / `LoginForm` off manual `<u>`

**Done when:** Storybook Controls can set keys; login/dialog samples use the API; no double-wrapping of existing React-tree children.

---

## Phase 4 — First product surfaces (hybrid MVP in PHP)

**Work:** replace the current modern admin look with Retro OS dialogs, **without** a full desktop yet:

1. **Login** — `LoginPage` → `DialogWindow` + form atoms; keep the same props (`action`, CSRF, error) so Twig ([`templates/security/login.html.twig`](../../webhemi-php/templates/security/login.html.twig)) does not break.
2. **One internal surface** — e.g. Settings or Control Panel `IconPanelWindow` (Sites/Hosts/… icons) linking to existing routes / opening windows later.

Export via [`admin/index.ts`](../../webhemi-ui/src/admin/index.ts) + PHP controller re-exports with unchanged names (`LoginPage`, …).

**Hard parts:**
- AssetMapper only sees built `dist` — local loop: UI build + `sync-ui` (or `make up` watch) is required.
- Login is not in the desktop shell today; full-bleed dialog vs `body.dashboard` background — use teal desktop background on login, no taskbar.
- Old `AdminLayout` / Sidebar / TopBar **live in parallel** until all pages migrate — avoid a half-migrated layout on one page.

**Done when:** `https://…/login` shows a Retro OS dialog after sync; CSRF / error messaging still works.

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

**Work:** put `SitesPage`, `HostsPage`, list views, etc. into Retro OS windows / heading-panel layout (rewritten brick) + table / form layouts. Routing:
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
- Hub README / architecture doc: Admin Theme = Retro OS owned chrome; themes are self-contained.

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
| 3b | 1–2 days | accessKey helper edge cases (non-string children) |
| 4 | 3–5 days | PHP sync + Login parity |
| 5 | 1.5–3 weeks | Window manager fidelity |
| 6–7 | ongoing | Page migration, breaking cleanup |

---

## Explicitly out of scope

- Making frontend (site) themes Retro OS
- Sharing admin98 chrome/icons/scripts with frontend themes (admin-only by design)
- Replacing `webhemi-js` / Payload admin
- Publishing admin98 as its own NPM package
- Reintroducing the npm `98.css` dependency

---

## Phase checklist

- [x] Phase 0 — Integration contract
- [x] Phase 1 — Styles into `webhemi-ui` (scoped)
- [x] Phase 2 — React chrome atoms + Storybook
- [x] Phase 3 — Product layout bricks + SystemIcon; scrollbar as chrome capability
- [ ] Phase 3b — Dynamic accessKey (Button + FieldRow); see AccessKey_Dynamize.md
- [ ] Phase 4 — Retro OS Login + one Control Panel surface via PHP
- [ ] Phase 5 — React AdminDesktop shell
- [ ] Phase 6 — Migrate Sites/Hosts/… into windows
- [ ] Phase 7 — Remove legacy admin UI; docs/changelog; sandbox role update
