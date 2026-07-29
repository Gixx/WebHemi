# Borrowed Storybook repo — practices for `@webhemi/ui`

**Source (local):** `/home/dev/Projects/story` (`@ces/whitelabel-frontend-libs` / dgtls-storybook whitelabel monorepo)  
**Audience:** WebHemi hub + [`webhemi-ui`](../../webhemi-ui/)  
**Intent:** Inspiration and best-practice notes only. **Do not copy code, brand assets, Chromatic tokens, or domain features.**

Reviewed: Storybook configs, story organization, MSW, testing/Chromatic, component folders, scaffold, agent docs, Symfony-oriented bundling, shared Tailwind/SVG packages. Compared mentally to current `@webhemi/ui` (React + Vite Storybook, Admin Retro OS + Default theme, PHP AssetMapper via `sync-ui`).

---

## 1. What that repo is

An **npm-workspaces monorepo** with two Storybook apps and shared packages:

| Piece | Role |
|--------|------|
| `apps/twig-components` | Primary marketing/CMS design system: Twig + Stimulus + Tailwind; Storybook on **web-components + Webpack 5** |
| `apps/react-components` | React Aria / CVA component library + **product feature islands** (configurator, forms, tables, maps); Storybook on **React + Vite** |
| `packages/tailwind-config` | Shared Tailwind preset (tokens, brand variants) |
| `packages/svgs`, `stimulus`, `lotties` | Shared assets / Stimulus helpers |
| `cli/custom-project` | Customer fork bootstrap |
| Root `AGENTS.md` | Single entry for AI agents (scripts, conventions, pointers) |

Delivery model: frontend team builds **shippable dist/bundles**; Symfony (or similar) mounts them — analogous in spirit to WebHemi’s `@webhemi/ui` → AssetMapper path, but with heavier multi-bundle and Twig stacks.

```mermaid
flowchart LR
  subgraph storyRepo [story monorepo]
    TwigSB[Twig Storybook]
    ReactSB[React Storybook]
    TW[@ces/tailwind-config]
    Bundles[Vite feature bundles]
  end
  TW --> TwigSB
  TW --> ReactSB
  ReactSB --> Bundles
  Bundles -.-> SymfonyHost[Symfony / Encore host]
  TwigSB -.-> SymfonyHost
```

For WebHemi, the **React app** is the closer analogue. Twig Storybook is useful for **process** (scaffold, MDX docs, atomic naming), not for stack choice.

---

## 2. How they run Storybook

### React app (`.storybook/`)

- **Framework:** `@storybook/react-vite`
- **Stories:** `src/components/**/*.mdx` + `src/**/*.stories.tsx`
- **Addons (lean):** primarily `@storybook/addon-themes` in `main.ts` (docs/a11y may live elsewhere or via framework defaults)
- **`viteFinal`:** `@` → `src` alias
- **Docgen:** `react-docgen-typescript`
- **`preview`:** global Tailwind CSS; **MSW** `initialize({ onUnhandledRequest: 'bypass' })` + `mswLoader`; backgrounds; `storySort.order: ['features', 'components', 'pages']`; theme decorator via `withThemeByClassName` (brand + light/dark **class names** on the root)
- **Manager:** branded theme (logo, fonts, colors) — vendor chrome

### Twig app (contrast)

- Webpack 5 + Twig loader, Stimulus on `window.App`, custom addons (PDF Reactor, dark/light switcher), viewport presets aligned to Tailwind breakpoints, global `tags: ['autodocs']`, rich MDX under `src/docs/`
- Can **serve React `bundles/`** as static `/bundles` when present (hybrid preview) — only relevant if you ever embed React islands inside Twig stories (WebHemi does not need this)

**Takeaway vs WebHemi today:** `@webhemi/ui` already uses React + Vite, a11y, docs, Vitest, Chromatic addons, and `data-wh-theme` scoping. The borrowed repo’s useful delta is **theme-by-className as a first-class addon pattern**, **explicit storySort**, **MSW as optional story infrastructure**, and **manager branding** — not a second Storybook stack.

---

## 3. Story and folder conventions

### Twig (strong discipline)

Atomic prefixes under `src/elements/`:

- `a-` atom, `c-` component, `b-` brick, `t-` template, `p-` page  
- Co-located: template, styles, types, data fixtures, stories, docs/MDX, optional Stimulus controller  
- **CLI scaffold** (`npm run scaffold -- <prefix>-<name> [-c]`) generates the folder and wires CSS / Stimulus indexes  

### React

- `src/components/<PascalCase>/` — component, types, `index.ts`, stories, optional `*.data.ts` / CSS / MSW  
- `src/features/<kebab-case>/` — product apps with `create-*-renderer.tsx` mount entrypoints  
- Variants often via **CVA** (sometimes `tailwind-variants`), class names frequently **BEM-like** to stay visually aligned with Twig (`a-button-link--primary`, etc.)  
- Interaction primitives: **React Aria Components**; some public props via **Zod** schemas  

**WebHemi alignment:** You already moved Admin chrome/bricks to **one folder per atom/brick** + `_lib`. Keep that. Optional next step: co-locate `*.data.ts` fixtures for complex bricks (Control Panel icons, wizard steps) the way they co-locate Accordion/Button fixtures — without adopting `a-/c-/b-` Twig prefixes (Admin already uses Atoms / Bricks in Storybook titles).

---

## 4. MSW and data for interactive stories

- Global MSW loader in preview; worker under `public/`  
- Handlers **next to the feature** (`msw/*.handlers.ts` or `*.msw.ts`); stories declare `parameters.msw.handlers`  
- Shared helpers for common POSTs; advanced features use **stateful** mock APIs (e.g. tree/configurator)  
- Package export generators **exclude** `msw` paths so mocks never ship to consumers  

**WebHemi:** Skip MSW until Admin surfaces need fetch (lists, save dialogs, Phase 5–6 shell). When you do: co-locate handlers, `bypass` unhandled requests, never export mocks from `@webhemi/ui`. Prefer fixtures + props for Login / Control Panel MVP (already Twig-driven CSRF/props).

---

## 5. Testing and visual regression

| Layer | Their practice |
|--------|----------------|
| Unit | Vitest `*.test.ts` (node project) |
| Browser / interaction | Vitest **browser mode** + Playwright + `vitest-browser-react` (`*.browser.test.tsx`) — **Storybook `play` is rare/absent** |
| Visual | Chromatic `--only-changed`, path-filtered CI jobs per app |
| Lint | oxlint + ESLint; Twig Prettier for templates |

**WebHemi:** You already have Storybook Vitest + Chromatic workflows and at least one `play` on IconPanelWindow. Inspiration: treat **browser Vitest** as the home for heavy widgets (table selection, window drag math later), keep **`play`** for thin Storybook Guide / smoke demos; keep Chromatic tokens in **CI secrets** (their `package.json` embeds project tokens — do not copy that habit).

---

## 6. Shipping to PHP hosts

React features build via Vite bundler configs into `bundles/<name>/` ESM, with a **DOM root + JSON bootstrap** (`application/json` script tag → `createRoot`). Form renderer uses **PostCSS prefix scoping** so CSS does not leak into the host page. Twig app builds Stimulus + CSS with tsup for integration.

**WebHemi:** You already ship a **single** `@webhemi/ui` library + CSS via AssetMapper/`react_component` — simpler and better for zero-Node PHP prod. Steal the *ideas*:

1. Clear **mount contract** (root id + props) documented once for Twig authors  
2. **Theme CSS isolation** (you use `[data-wh-theme="admin"]`; they use prefix scopes for islands) — keep hardening so Default/site never imports Admin chrome  
3. Avoid proliferating many micro-bundles unless a feature truly cannot live in the main package  

---

## 7. Shared tokens and icons

- One Tailwind preset package consumed by both apps (colors, spacing, dark/light variants, CMS safelist)  
- SVG package builds React components + Zod schema from categorized icon folders  

**WebHemi:** Themes stay **self-contained** (contract). Still useful: a small **platform** token layer (you have `platform.css`) documented as the only shared non-UI layer; icon sets stay Admin-only vs theme-owned. Full SVG build pipelines are overkill until icon volume hurts.

---

## 8. Documentation and agent workflow

- **`AGENTS.md`** at repo root: scripts table, apps vs packages, path aliases, do-not-edit generated files, scaffold, where to read more  
- Points to **`.cursor/docs/COMPONENT_CREATION_REFERENCE.md`** — but **`.cursor` is gitignored**, so that reference was **missing in this clone** (anti-pattern for durable docs)  
- Twig: rich in-Storybook MDX (basics, theme, per-component docs with Figma links)  
- Conventional commits; root scripts delegate to workspaces  

**WebHemi:** Put creation/runbooks under hub **`docs/`** (you already use `docs/plan/` and now `docs/analysis/`). Consider a short **`webhemi-ui/AGENTS.md`** (or hub `docs/` pointer) mirroring scripts (`storybook`, `build`, `sync-ui`) and Admin folder rules — durable, not Cursor-only.

---

## 9. What works well vs what is heavyweight

**Works well**

- Clear split: **primitives in Storybook** vs **shippable mounts** for PHP  
- Theme switching by **root class / attribute**, not backgrounds-as-theme  
- MSW co-located and excluded from package exports  
- Vitest unit + real browser projects for interaction-heavy widgets  
- Scaffold + agent guide for consistent contributions (Twig)  
- Generated CSS indexes / export maps to reduce barrel churn  
- Chromatic scoped with `--only-changed`  

**Heavyweight (avoid for WebHemi scale)**

- Two full Storybook stacks (Webpack Twig WC + Vite React)  
- Vendor addons (PDF Reactor), Pimcore brick registry, CMS editmode safelists  
- Large domain features as the “design system” center of gravity  
- Hardcoded Chromatic tokens and customer-fork CLI  
- Manager theme colors duplicated instead of derived from design tokens  

---

## 10. Practices worth adopting in `@webhemi/ui` (priority)

Ordered for near-term Admin98 work (Phases 4–5), inspiration only:

1. **Keep one React Storybook**; deepen **theme decorator** clarity (`admin` / `default`) and document Docs vs canvas scoping (you already special-case Docs `html` theme — keep that documented in one place).  
2. **Story Sort / sidebar IA** as the tree grows: e.g. `Admin/Atoms`, `Admin/Bricks`, `Admin/Surfaces`, `Themes/Default` — explicit `parameters.options.storySort` when titles drift.  
3. **Fixture co-location** (`*.data.ts`) for bricks and future surfaces (Control Panel icons, dialog copy) instead of inline mega-arrays in stories.  
4. **Component creation checklist** in hub `docs/` (folder layout, stories Controls, theme scope, no Admin imports into Default) — the durable version of their missing COMPONENT_CREATION_REFERENCE.  
5. **Optional `AGENTS.md`** in `webhemi-ui` with root/hub scripts and “do not edit generated dist” rules.  
6. **MSW later**, only for API-backed Admin windows; exclude from exports.  
7. **Browser Vitest** for shell math / tables when Phase 5 lands; keep Chromatic on chrome atoms first (your Phase 7 note already says this).  
8. **Light scaffold** (Plop/hygen) only if atom/brick count keeps growing and drift becomes real — copy the *idea*, not Twig templates.  
9. **Document the PHP mount contract** once (props, CSRF, `data-wh-theme`) next to sync-ui — their renderer README pattern.  
10. **Controls/Docs discipline** you started with `accessKey` (`controls.include`, Accessibility category) — keep applying to new public props so Docs stay usable.

---

## 11. Explicitly do **not** copy

| Skip | Why |
|------|-----|
| Twig / Stimulus / Webpack Storybook | Wrong stack for `@webhemi/ui` |
| CES/valantic branding, Figma whitelabel URLs, manager fonts | Vendor |
| Hardcoded `chpt_*` Chromatic tokens | Secrets → CI env only |
| `a-/c-/b-` Twig taxonomy as mandatory prefixes | Optional naming only; WebHemi already has Admin Atoms/Bricks |
| Pimcore brick registry / editmode safelist | CMS-specific |
| Configurator, locking-systems, dealer-map, MobX/Redux feature architecture | Product domain, not DS |
| Full SVG monorepo pipeline | Overkill until icon scale demands it |
| Serving React bundles into Twig Storybook | Dual-app concern |
| Customer `custom:project` CLI / Bitbucket pipeline shape | Ops specifics |
| Blind global `autodocs` without Controls curation | Docs noise |
| Any concrete source files as paste-base | Inspiration only |

---

## 12. Suggested follow-ups (optional, not committed work)

- Add `docs/plan/` or `webhemi-ui` checklist: “New Admin atom/brick” (folder, stories, theme, exports).  
- When Phase 4 PHP Login lands: one-page “Mount contract” under hub docs.  
- Revisit MSW + browser Vitest when the first Admin list page leaves pure props.  
- Link this note from the hub README docs table if you want it discoverable long-term.  
- **Manager branding (started):** pixel logo at [`webhemi-ui/src/admin/assets/logo/webhemi.svg`](../../webhemi-ui/src/admin/assets/logo/webhemi.svg), wired via `.storybook/manager.ts` + `staticDirs` → `/brand/`. Optional later: horizontal wordmark if the square mark feels tight in the sidebar.

---

## References (paths in the borrowed tree)

- Root: `README.md`, `AGENTS.md`, `package.json`  
- React Storybook: `apps/react-components/.storybook/main.ts`, `preview.ts`, `manager.ts`  
- Twig Storybook: `apps/twig-components/.storybook/main.ts`, `preview.ts`  
- Themes decorator: `apps/react-components/src/lib/storybook/decorators/decoratorAddonThemes.ts`  
- Scaffold: `apps/twig-components/utils/tasks/scaffold/`  
- Example atom stories: `apps/react-components/src/components/Button/Button.stories.tsx`  
- Feature + MSW: `apps/react-components/src/features/register-portal-locking-systems/`  

WebHemi counterparts for comparison: [`webhemi-ui/.storybook/`](../../webhemi-ui/.storybook/), [`docs/plan/Admin98_Product_Integration.md`](../plan/Admin98_Product_Integration.md), [`docs/local-dev.md`](../local-dev.md).
