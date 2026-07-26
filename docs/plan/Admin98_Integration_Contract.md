# ADR — Admin98 → WebHemi integration contract

**Status:** Accepted (Phase 0)  
**Date:** 2026-07-26  
**Parent plan:** [Admin98_Product_Integration.md](./Admin98_Product_Integration.md)  
**Reference sandbox:** [webhemi-admin98](https://github.com/Gixx/webhemi-admin98) (local sibling or clone; not an NPM dependency)

This document is the **integration contract** for bringing the Win98 admin tech demo into `@webhemi/ui` and WebHemi.PHP. Later phases implement against these rules; they do not renegotiate them without an ADR update.

---

## Context

WebHemi needs a real Admin Theme (Win98-inspired) plus independently swappable frontend (site) themes. The sandbox proved chrome atoms, product layouts, and a desktop shell. Product delivery must live in `webhemi-ui` and sync into PHP via AssetMapper — zero Node in production.

Storybook already switches themes with `data-wh-theme` on `<html>`. The sandbox used a separate `body.dashboard` marker for shell layout. Keeping both as required contracts would create two parallel “this is admin” signals and fight the existing Storybook toolbar.

---

## Decisions

### D1 — Document home

Canonical contract and phase plan live under the **hub** `docs/plan/`. Package READMEs may link here; they are not a second source of truth.

### D2 — Single theme scope: `[data-wh-theme="admin"]`

| Rule | Detail |
|------|--------|
| **Scope root** | Admin chrome CSS, Win98 tokens, and admin product styles apply **only** under `[data-wh-theme="admin"]` (set on `<html>`). |
| **Not contracted** | `body.dashboard` and `.wh-admin` are **not** part of the product contract. |
| **Storybook** | Keep the existing toolbar → `document.documentElement.setAttribute('data-wh-theme', theme)`. Do not invent a parallel theme system. |
| **PHP** | Admin surfaces set `data-wh-theme="admin"` on `<html>` (wired in Phase 0). Future site templates use `default` (or another frontend theme id). |
| **Login vs desktop** | Both use the same theme attribute. Layout differences (full-bleed login dialog vs shell with taskbar) live in **React / product CSS**, not a second body class. |

**Why:** One switch aligns Storybook, CSS scoping, and PHP. Sandbox `body.dashboard` was a demo document shell; product shell behavior is re-expressed under the theme scope so Default frontend stories never inherit Win98 `button` / `input` rules.

### D3 — Markup contract (chrome)

| Rule | Detail |
|------|--------|
| **Class names** | Keep 98-compatible names: `.window`, `.title-bar`, `.field-row`, `.sunken-panel`, `ul.tree-view`, etc. |
| **No rename wave** | Do **not** introduce a `wh-` prefix rename in the first port. Optional prefixes are a later, explicit decision. |
| **Title controls** | Preserve `aria-label` values expected by chrome/scripts (Minimize / Maximize / Restore / Help / Close). |
| **Adjacency** | Form controls that rely on `input + label` must keep that DOM adjacency in React (no wrapper that breaks `+` selectors). |
| **Tabs** | Nested `.window[role=tabpanel]` is a tab panel, not a shell window. Product shell code must distinguish (e.g. `data-shell-window` / context) — same lesson as the sandbox. |

**Sandbox fidelity:** admin98 is a **visual and UX reference**. `@webhemi/ui` may adapt markup or selectors for cleaner system integration (e.g. dropping `body.dashboard`). **End-user experience** (look, interaction, a11y affordances) must remain equivalent; byte-identical HTML is not required.

### D4 — Theme ownership (no shared design layer)

| Rule | Detail |
|------|--------|
| **Self-contained themes** | Every theme owns its tokens, styles, and React chrome/atoms/bricks. A third party should be able to add a frontend theme by copying the theme folder pattern without depending on Admin internals. |
| **No shared UI kit** | `src/shared/components/*` is **not** the long-term model. Current shared atoms move into `themes/default` (or are rewritten there). Admin does **not** import Default components. |
| **Admin rewrite** | Current `src/admin/**` (modern CMS layout, tokens, pages) is **throwaway**. Win98 Admin is a full rewrite under `src/admin/`, not an incremental restyle of Sidebar/TopBar/etc. |
| **Allowed non-theme code** | Thin infrastructure only, e.g. `src/lib/cn.ts` (clsx helper). No buttons, inputs, or theme CSS in `lib/`. |
| **CSS entries** | Admin production CSS is theme-scoped and does not pull frontend theme chrome. Frontend themes do not import Admin chrome SCSS. |
| **Timing** | Phase 0 documents this; **file moves / deletes happen in Phase 1+** so the live PHP admin keeps working until Win98 surfaces replace it (see parent plan Phase 4–7). |

### D5 — Package and delivery

| Rule | Detail |
|------|--------|
| **Target** | All product UI ships from `webhemi-ui` (`@webhemi/ui`). |
| **Sandbox role** | Reference / manual regression until Storybook parity; then optional archive. Not published as NPM. |
| **Pipeline** | `npm run build` → `dist/` → `bin/sync-ui.sh` / `composer run sync-ui` → AssetMapper + `react_component(...)`. |
| **No npm `98.css`** | Owned chrome SCSS in-tree (as in the sandbox), not the upstream package. |

### D6 — Layers (product mental model)

```text
[data-wh-theme="admin"]
  ├── tokens          # Win98 / WebHemi admin CSS variables (+ @theme as needed)
  ├── chrome          # Control look: button, window, field-row, tabs, …
  ├── product         # Shell + layouts: desktop, toolbar, pane layouts, scrollbar skin
  └── react           # Atoms → bricks → pages / AdminDesktop
```

Frontend themes (e.g. `themes/default`) mirror the same idea for **site** UI: own tokens + components, separate tree, selected via `data-wh-theme="default"` (or future ids).

| Layer | Responsibility | Must not |
|-------|----------------|----------|
| Tokens | Colors, bevel vars, chrome font metrics | Style global `button` outside theme scope |
| Chrome | 98-compatible control appearance | Depend on desktop/taskbar |
| Product | Admin shell UX and pane layouts | Leak into Default theme CSS |
| React | DOM contract + behavior ports | Break adjacency selectors; treat tabpanel `.window` as shell window |

### D7 — What stays in the sandbox

Until Storybook parity (parent plan Phase 2–5):

- `catalog.html` — chrome visual checklist  
- `structure.html` / `index.html` — layout and shell behavior checklist  
- Owned SCSS/JS — copy/adapt source, not runtime dependency  

After parity: README pointer that canonical stories live in `webhemi-ui`; catalog optional or archived.

### D8 — Explicitly out of scope (unchanged)

- Making frontend themes Win98  
- Sharing Admin chrome/icons/scripts with frontend themes  
- Replacing `webhemi-js` / Payload admin  
- Publishing admin98 as its own NPM package  
- Reintroducing npm `98.css`  
- Trade dress / IP claims (architecture only)

---

## Phase 0 runtime wiring

As of this ADR acceptance:

- Hub docs: this contract + parent phase plan checklist.  
- PHP: `<html data-wh-theme="admin">` on the base layout used by login and admin pages (overridable later for site surfaces).

No Admin98 SCSS/React port in Phase 0 (that is Phase 1+).

## Phase 1 note

Styles now live under `webhemi-ui/src/admin/styles/` with `meta.load-css` scoping to `[data-wh-theme="admin"]`. Production CSS is built via Vite (`vite.css.config.ts`), not the Tailwind CLI. See parent plan Phase 1.
---

## Consequences

**Positive**

- One theme attribute for Storybook, CSS, and PHP.  
- Clear rule for third-party frontend themes.  
- Admin Win98 work is unblocked without preserving the modern admin tree.  
- Sandbox remains a side-by-side UX checklist without blocking product-shaped markup.

**Trade-offs**

- Temporary dual world: legacy modern admin still runs until Phase 4+ replaces surfaces.  
- Contributors must not “quickly share” a Button between Admin and Default.  
- CSS authors must nest chrome under `[data-wh-theme="admin"]` or Default stories break.

**Follow-up**

- Phase 1: scoped style system under `webhemi-ui/src/admin/styles/`.  
- Relocate or rewrite former `shared` atoms into `themes/default` as Default work proceeds.  
- Delete legacy admin UI in Phase 7.

---

## Decision log (Phase 0 workshop)

| # | Choice | Notes |
|---|--------|--------|
| 1 | Hub `docs/plan/` | With parent integration plan |
| 2 | `[data-wh-theme="admin"]` only | No `body.dashboard` / `.wh-admin` contract |
| 3 | PHP attribute in Phase 0 | Early runtime alignment |
| 4 | No shared UI; admin rewrite | Themes self-define; `lib/` OK for helpers |
| 5 | Phase 0 = ADR + PHP only | Moves/deletes in Phase 1+ |
| 6 | `src/lib/` allowed | e.g. `cn` / clsx — no UI |
